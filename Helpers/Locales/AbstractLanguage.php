<?php

namespace Helpers\Locales;

abstract class AbstractLanguage implements LocaleInterface {
    public string
        $active            = 'Active',
        $add               = 'Add',
        $addLevel          = 'Add level',
        $addPlan           = 'Add plan',
        $addProject        = 'Project adding',
        $after             = 'after',
        $badPassword       = 'Wrong password',
        $chat              = 'Chat',
        $check             = 'check',
        $close             = 'Close',
        $deposit           = 'Deposit',
        $description       = 'Description',
        $download          = 'Download',
        $email             = 'Email',
        $emailConfirmation = 'Email confirmation',
        $emailConfirmSent  = 'Confirmation code is sent to your email',
        $emailIsBusy       = 'This email is already registered. Please enter another',
        $enter             = 'Enter',
        $error             = 'Error',
        $exit              = 'Exit',
        $free              = 'free',
        $freeForAddProject = 'Adding a project to the database is completely',
        $from              = 'from',
        $general           = 'General',
        $guest             = 'Guest',
        $headKeywords      = 'hyip monitoring 2020, profitable projects, capital, investments',
        $headDescription   = 'High Yield Investment Projects 2020',
        $headTitle         = 'Real Investment Market',
        $lang              = 'en',
        $languages         = 'Site languages',
        $level             = 'level',
        $login             = 'Login',
        $loginIsBusy       = 'This login is already registered. Please enter another',
        $menu              = 'Menu',
        $name              = 'Name',
        $needAuthorization = 'You need to log in',
        $no                = 'No',
        $noAccess          = 'No access',
        $noConfirmCode     = 'Confirmation code is not found',
        $noLanguage        = 'Language is not found',
        $noUser            = 'User is not found',
        $noPage            = 'Page is not found',
        $noProject         = 'Project is not found',
        $notPublished      = 'Not published',
        $options           = 'Options',
        $or                = 'or',
        $orCopyLink        = 'Or copy this link into your browser',
        $password          = 'Password',
        $paymentSystem     = 'Payment systems',
        $period            = 'Period',
        $plans             = 'Investment plans',
        $preview           = 'Preview',
        $profit            = 'Profit',
        $projectName       = 'Project name',
        $projectIsAdded    = 'Project is added',
        $projectUrl        = 'Project\'s url or referral link',
        $refProgram        = 'Referral program',
        $registration      = 'Registration',
        $remember          = 'Remember',
        $remove            = 'Remove',
        $repeatPassword    = 'Repeat password',
        $screenshot        = 'Site\'s screenshot',
        $selectFile        = 'Select a file',
        $sendForm          = 'Send form',
        $showAllLangs      = 'Show all languages',
        $siteExists        = 'Site already exists',
        $siteIsFree        = 'Site is free',
        $startDate         = 'Start date of project',
        $success           = 'Success',
        $userRegistered    = 'User is registered',
        $userRegistration  = 'User\'s registration',
        $verifyAccount     = 'Verify my account',
        $view              = 'View',
        $welcomeTo         = 'Welcome to',
        $writeMessage      = 'Write a message...',
        $wrongUrl          = 'Wrong site address',
        $yes               = 'Yes',
        $youAreAuthorized  = 'You are authorized';

    public array
        $paymentType       = ['Withdrawal', 'Manual', 'Instant', 'Automatic'],
        $periodName        = ['', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
        $currency          = ['dollar', 'euro', 'bitcoin', 'ruble', 'pound', 'yen', 'won', 'rupee'];

    abstract public function getPeriodName(int $i, int $k): string;
}