<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Primary Prefix/Locale
    |--------------------------------------------------------------------------
    |
    | This prefix will be prepended to the view and will be searched for first.
    | In most cases this will be the current locale.
    |
    */
    'primary' => Config::get('app.locale'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Prefix/Locale
    |--------------------------------------------------------------------------
    |
    | This prefix will be prepended to the view and will be searched for
    | when the view is not found in the primary location.
    | In most cases this will be the fallback locale.
    |
    */
    'fallback' => Config::get('app.fallback_locale'),

    /*
    |--------------------------------------------------------------------------
    | List of additional Prefixes/Locales
    |--------------------------------------------------------------------------
    |
    | If the primary and fallback locations are invalid
    | these additional prefixes will be used to find the view.
    |
    | If you want, you can add an array to the main app/config/app.php file:
    | 'locales' => ['nl' => 'Dutch', 'en' => 'English', 'fr' => 'French']
    | and swap the following 'prefixes' setting.
    |
    | This way, you can also use they locale values for PHP's
    | setlocale(LC_ALL, $locales[$locale]);
    |
    */
    //'prefixes' => array_keys(Config::get('app.locales')),
    'prefixes' => [],

    /*
    |--------------------------------------------------------------------------
    | View Directory Separator
    |--------------------------------------------------------------------------
    |
    | Do you prefer 'en.folder.view' or 'en/folder/view'?
    |
    */
    'divider' => '.',

];