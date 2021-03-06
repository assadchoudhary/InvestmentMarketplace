<?php

namespace Helpers\Locales;

use Models\Constant\Language;

class SiteLanguageCollection
{
    public CONST LANGUAGES = [
        Language::EN => En::class,
        Language::RU => Ru::class,
        Language::ZH => Zh::class,
        Language::BN => Bn::class,
        Language::ES => Es::class,
        Language::TR => Tr::class,
        Language::JA => Ja::class,
    ];

    public static function getByShortname(string $shortname): AbstractLanguage {
        $languageClass = self::LANGUAGES[Language::getValue($shortname)];
        return new $languageClass();
    }
}
