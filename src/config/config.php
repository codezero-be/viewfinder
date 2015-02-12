<?php

return [

    /**
     * PHP Locale Category
     *
     * @link http://php.net/setlocale
     * @link http://php.net/manual/en/function.strftime.php
     *
     * This will allow for automatic translation
     * of the PHP strftime() output etc.
     */
    'phpLocaleCategory' => LC_ALL,

    /**
     * List of Locales
     *
     * These locale keys will be used as prefixes to search for views.
     * and to set Laravel's locale and fallback_locale settings.
     * The locales array values will be used to set the PHP locale.
     * To see the available locales on your server run "locale -a".
     *
     * @link http://php.net/setlocale
     *
     * You can publish this config file and edit it or add an array
     * directly to Laravel's config/app.php file:
     * 'locales' => ['nl' => 'nl_BE.utf8', 'en' => 'en_US.utf8', 'fr' => 'fr_FR.utf8'],
     */
    'locales' => Config::has('app.locales')
        ? Config::get('app.locales')
        : ['en' => 'en_US.utf8'],

];