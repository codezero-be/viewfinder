<?php

return [

    /**
     * Primary Prefix/Locale
     *
     * This prefix will be prepended to the view name
     * and will be searched for first.
     */
    'primaryPrefix' => Config::get('app.locale'),

    /**
     * Fallback Prefix/Locale
     *
     * This prefix will be prepended to the view name
     * and will be searched for when the view is not
     * found in the primary location.
     */
    'fallbackPrefix' => Config::get('app.fallback_locale'),

    /**
     * List of Prefixes/Locales
     *
     * If a view isn't found in the primary or fallback location,
     * these prefixes will be used to continue the search.
     *
     * If you want, you can add an array to the main config/app.php file:
     * 'locales' => ['nl' => 'Dutch', 'en' => 'English', 'fr' => 'French'],
     *
     * This way, you can also use these locale values for PHP's localization:
     * setlocale(LC_ALL, $locales[$locale]);
     *
     * You may include the primary and fallback locale in this array.
     */
    'prefixes' => Config::has('app.locales') ? array_keys(Config::get('app.locales')) : [],

];