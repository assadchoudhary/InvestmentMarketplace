<?php

namespace Controllers;

use Core\{Controller, Database, View};
use Dto\ErrorRoute;
use Helpers\{
    Output,
    Data\Currency,
};
use Models\Collection\{
    Languages,
    MVProjectCounts,
    ProjectChatMessages,
    MVProjectLangs,
    Payments,
    ProjectLangs,
    Projects,
    MVProjectFilterAvailableLangs,
    MVProjectSearchs,
    Users};
use Models\Table\{Language, Project, ProjectChatMessage, ProjectLang, Queue, Redirect};
use Models\MView\MVProjectLang;
use Models\Constant\{ProjectStatus, User, Views};
use Requests\Investment\{AddRequest,
    ChangeStatusRequest,
    ChatMessageRequest,
    ChatMessagesRequest,
    CheckSiteRequest,
    DetailsRequest,
    RedirectRequest,
    ReloadScreenshotRequest,
    SetChatMessageRequest,
    ShowRequest};
use Services\InvestmentService;
use Traits\AuthTrait;
use Views\Investment\{Added, Details, DetailsMeta, ProjectFilter, Registration, Show, NoShow};

class Investment extends Controller {
    use AuthTrait;
    private CONST
        LIMIT = 20;

    public function registration(): Output {
        $params = [
            'payments'                  => new Payments(),
            'mainProjectLanguages'      => new Languages('pos is not null', 'pos asc'),
            'secondaryProjectLanguages' => new Languages('pos is null and id > 0'),
            'currency'                  => Currency::getCurrency(),
        ];

        Output()->addView(Registration::class, $params);
        return Output()->addFunction('ProjectRegistration');
    }

    public function show(ShowRequest $request): Output {
        $MVProjectFilterAvailableLangs = new MVProjectFilterAvailableLangs(['status_id' => $request->status]);

        if (!$MVProjectFilterAvailableLangs->count()) {
            // без фильтра
            return $this->noShow([Views::PROJECT_FILTER => '']);
        }
        $languages = new Languages(['id' => $MVProjectFilterAvailableLangs->getValuesByKey()]);
        /** @var Language $pageLanguage текущий язык*/
        $pageLanguage = $languages->getByKeyAndValue('shortname', $request->lang);

        $projectFilter = (new View(ProjectFilter::class, [
            'request'                       => $request,
            'url'                           => Router()->getRoute()->generateUrl(),
            'languages'                     => $languages,
            'MVProjectFilterAvailableLangs' => $MVProjectFilterAvailableLangs,
            'pageLanguage'                  => $pageLanguage ?? new Language(['flag' => 'xx']), // фэйк
        ]));

        if (!$pageLanguage) {
            return $this->noShow([Views::PROJECT_FILTER => $projectFilter]);
        }

        // ID найденных проектов
        $projectSearchs = new MVProjectSearchs([
            'lang_id' => $pageLanguage->id,
            'status_id' => $request->status,
        ], min(self::LIMIT, $MVProjectFilterAvailableLangs->{$pageLanguage->id}->cnt));

        if (!$projectSearchs->count()) {
            return $this->noShow([Views::PROJECT_FILTER => $projectFilter]);
        }

        $projectIds     = $projectSearchs->getValuesByKey();
        $projects       = new Projects(['id' => $projectIds]);
        if (!$projects->count()) {
            return $this->noShow([Views::PROJECT_FILTER => $projectFilter]);
        }
        $MVProjectLangs = new MVProjectLangs(['id' => $projectIds]);
        $payments       = new Payments(['id' => $projects->getUniqueValuesByKey('id_payments')]);
        $projectLangs   = new ProjectLangs(['project_id' => $projectIds, 'lang_id' => $pageLanguage->id]);

        $pageParams = [
            'projects'            => $projects,
            'MVProjectLangs'      => $MVProjectLangs,
            'pageLanguage'        => $pageLanguage,
            'payments'            => $payments,
            'projectLangs'        => $projectLangs,
            'languages'           => $languages,
            'isAdmin'             => CurrentUser()->isAdmin(),
            Views::PROJECT_FILTER => $projectFilter,
        ];

        Output()->addFunctions([
            'setStorage' => ['lang' => $pageLanguage->id, 'chat' => []],
            'initChat',
            'panelScrollerInit',
            'imgClickInit',
            'loadRealThumbs',
            'checkChats',
        ], Output::DOCUMENT);

        return Output()->addView(Show::class, $pageParams);
    }

    public function details(DetailsRequest $request): Output {
        $project = (new Project())->getRowFromDbAndFill(['url' => $request->site]);
        if (!$project->id) {
            Output()->addHeader(Output::E404);
            return Router()->route(new ErrorRoute(Translate()->error, Translate()->noProject));
        }

        $language = (new Language())->getRowFromDbAndFill(['shortname' => $request->lang]);
        if (!$language->id) {
            Error()->add('lang', Translate()->noLanguage, true);
        }

        $projectLang = (new ProjectLang())->getRowFromDbAndFill([
            'project_id' => $project->id,
            'lang_id' => $language->id,
        ]);
        if (!$projectLang->id) {
            Error()->add('lang', Translate()->noLanguage, true);
        }
        $MVProjectLang  = (new MVProjectLang())->getById($project->id);
        $payments       = new Payments(['id' => $project->id_payments]);
        $languages      = new Languages(['id' => $MVProjectLang->lang_id]);

        $pageParams = [
            'project'     => $project,
            'projectLang' => $projectLang,
            'payments'    => $payments,
            'languages'   => $languages,
            'language'    => $language,
        ];

        if (Output()->isLayoutEnabled()) {
            Output()->addAdditionalLayoutView(Views::META, DetailsMeta::class, $pageParams);
        }

        return Output()
            ->addFunctions([
                'setStorage' => ['lang' => $language->id, 'chat' => []],
                'initChat',
                'panelScrollerInit',
                'imgClickInit',
                'checkChats',
            ], Output::DOCUMENT)
            ->addView(Details::class, $pageParams);
    }

    private function noShow(array $pageParams): Output {
        return Output()->addView(NoShow::class, $pageParams);
    }

    public function add(AddRequest $request, CheckSiteRequest $checkSiteRequest): Output {
        Db()->startTransaction();

        $url = $this->getWebsiteUrl($checkSiteRequest);

        if (count(array_unique([
            count($request->plan_percents),
            count($request->plan_period),
            count($request->plan_period_type),
            ])) !== 1)
        {
            // Кол-во элементов отличается
            Error()->add('plans', Translate()->error . ': ' . Translate()->plans, true);
        }

        // Сохраняем проект
        $project            = new Project($request->toArray());
        $project->admin     = CurrentUser()->getId() ?? User::GUEST;
        $project->url       = $url;
        $project->ref_url   = (strpos($checkSiteRequest->website, 'http') === false ? 'https://' : '') . $checkSiteRequest->website;
        $project->status_id = ProjectStatus::NOT_PUBLISHED;
        $project->save();

        // Сохраняем описания
        foreach ($request->description as $langId => $description) {
            $projectLang              = new ProjectLang();
            $projectLang->project_id  = $project->id;
            $projectLang->lang_id     = $langId;
            $projectLang->description = str_replace("\n", '</br>', $description);
            $projectLang->save();
            unset($projectLang);
        }

        (new Queue([
            'action_id'  => Queue::ACTION_ID_SCREENSHOT,
            'status_id'  => Queue::STATUS_CREATED,
            'payload'    => [
                'project_id' => $project->id,
            ],
        ]))->save();

        return Output()
            ->addView(Added::class)
            ->addAlertSuccess(Translate()->success, Translate()->projectIsAdded);
    }

    public function changeStatus(ChangeStatusRequest $request): Output {
        static::adminAccess();

        (new InvestmentService())->changeStatus($request);

        return (new \Controllers\Users())->reloadPage();
    }

    public function reloadScreen(ReloadScreenshotRequest $request): Output {
        static::adminAccess();

        (new InvestmentService())->reloadScreen($request);

        return (new \Controllers\Users())->reloadPage();
    }

    private function getWebsiteUrl(CheckSiteRequest $request): string {
        $url = self::getParsedUrl(str_replace('www.', '', strtolower($request->website)));
        Error()->exitIfExists();

        if ((Project::setTable()->selectRow(['url' => $url]))) {
            Error()->add('website', Translate()->siteExists, true);
        }

        return $url;
    }

    public function checkWebsite(CheckSiteRequest $request, bool $getUrl = false): Output {
        $url = $this->getWebsiteUrl($request);
        if (CurrentUser()->isAdmin()) {
            (new InvestmentService())->parseInfo($url);
        }
        return Output()->addFieldSuccess('website', Translate()->siteIsFree);
    }

    private static function getParsedUrl(string $url): string {
        $urlParsed = parse_url($url);

        if (isset($urlParsed['scheme'], $urlParsed['host'])) {
            if (count(explode('.', $url)) < 2) {
                Error()->add('website', Translate()->wrongUrl, true);
            }
            return $urlParsed['host'];
        }

        if (isset($urlParsed['host'])) {
            return $urlParsed['host'];
        }

        if (isset($urlParsed['path'])) {
            return self::getParsedUrl('https://' . $urlParsed['path']);
        }

        return $url;
    }

    public function sendMessage(SetChatMessageRequest $request): Output {
        (new ProjectChatMessage([
            'user_id'       => CurrentUser()->getId(),
            'project_id'    => $request->project,
            'lang_id'       => $request->lang,
            'message'       => $request->message,
            'session_id'    => CurrentUser()->session_id,
        ]))->save();

        return Output()->addFunction('checkChats');
    }

    public function getChatMessages(ChatMessagesRequest $request): Output {
        if ($request->messages) {
            $sql = \implode(' UNION ALL ', \array_map(fn(ChatMessageRequest $param) => /** @lang PostgreSQL */
                "
                    (SELECT m.id, m.date_create, m.user_id, m.project_id, m.message, m.session_id, u.has_photo, u.name, 
                            m.lang_id = -1 as html
                    FROM message m
                    LEFT JOIN users u ON u.id = m.user_id
                    WHERE m.project_id = {$param->project_id} and m.id > {$param->id} and m.lang_id = ANY(ARRAY[{$request->lang},-1])
                    ORDER BY id desc
                    limit 50)
                ", $request->messages));

            $messages = Database::getInstance()->rawSelect($sql);

            if (\count($messages) === 0) {
                return Output()->addFunction('sleepAndCheckChats');
            }

            $path = '/assets/img';
            $ext = (WEBP ? 'webp' : 'jpg');
            $data = \array_column($messages, null, 'id');
            foreach ($data as &$message) {
                $avatar = &$message['avatar'];
                if ($message['has_photo'] === true) {
                    $avatar = "$path/user/{$message['user_id']}.$ext";
                } else {
                    $animal = (($message['user_id'] ?? $message['session_id']) - 1) % 30 + 1;
                    $avatar = "$path/avatars/$animal.$ext";
                }
                $message['me'] =
                    (($userId = $message['user_id']) !== null && CurrentUser()->getId() === $userId)
                    || $message['session_id'] === CurrentUser()->session_id;
                $message['name'] ??= $this->getRandomNameBySessionId($message['session_id']);
            }
            Output()->addFunction('setNewChatMessages', ['messages' => $data]);
        }
        return Output()->addFunction('sleepAndCheckChats');
    }

    private function getRandomNameBySessionId(int $sessionId) {
        return ['Domestic', 'Wild', 'Furry', 'Herbivorous', 'Dangerous', 'Ferocious', 'Poisonous', 'Agile', 'Clever',
                'Aggressive', 'Beautiful', 'brave', 'Strong', 'Smart', 'Hungry', 'Angry', 'Fast', 'Strong', 'Gracious'][$sessionId % 19]
            . ' '
            . ['Crocodile', 'Bunny', 'Bear', 'Cow', 'Cat', 'Dog', 'Donkey', 'Elephant', 'Frog', 'Giraffe',
                'Hamster', 'Horse', 'Dragon', 'Octopus', 'Kangaroo', 'Lamb', 'Raccoon', 'Parrot', 'Panda', 'Poulpe',
                'Ant-eater', 'Mouse', 'Lion', 'Turtle', 'Unicorn', 'Snake', 'Whale', 'Fish', 'Bull', 'Zebra'][($sessionId-1)%30];
    }

    public function redirect(RedirectRequest $request): Output {
        (new Redirect([
            'user_id' => CurrentUser()->getId(),
            'project_id' => $request->project,
            'session_id' => CurrentUser()->session_id,
        ]))->save();

        $project = (new Project())->getById($request->project);

        return Output()->disableLayout()->addRedirectHeader($project->ref_url);
    }
}
