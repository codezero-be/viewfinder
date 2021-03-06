# Laravel 5 Localized View Finder

[![Build Status](https://img.shields.io/travis/codezero-be/viewfinder.svg?branch=master)](https://travis-ci.org/codezero-be/viewfinder)
[![Packagist](https://img.shields.io/packagist/v/codezero/viewfinder.svg)](https://packagist.org/packages/codezero/viewfinder)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/viewfinder.svg)](https://packagist.org/packages/codezero/viewfinder)
[![License](https://img.shields.io/packagist/l/codezero/viewfinder.svg)](https://packagist.org/packages/codezero/viewfinder)

This package allows you to easily create [route structures](#localized-routing) for a multi language site. It also provides a convenient way to automatically find and return a [localized version](#load-a-view) of a view.

## Installation

Install this package through Composer:

    "require": {
    	"codezero/viewfinder": "3.*"
    }

After installing, update your `config/app.php` file to include a reference to this package's service provider in the providers array:

    'providers' => [
	    'CodeZero\ViewFinder\ViewFinderServiceProvider'
    ]

If you want, you can register the `ViewFinder` alias:

    
    'aliases' => [
        'ViewFinder' => 'CodeZero\ViewFinder\Facade\ViewFinder'
    ]

## Configuration

You can either add your locales directly in `config/app.php`...

    'locales' => [
        'nl' => 'nl_BE.utf8',
        'en' => 'en_US.utf8',
        'fr' => 'fr_FR.utf8'
    ],

... or you can publish our configuration file and add your locales there:

    php artisan vendor:publish

This will save the configuration file to `config/viewfinder.php`.

> **INFO:** The array key will be used to set Laravel's `locale` and `fallback_locale`. Its value will be used to set [PHP's locale](http://php.net/setlocale). Run `locale -a` to find out which locales your server supports. You should add your locales to the array in the order you want `ViewFinder` to search for fallbacks.

## Views Structure

If you are creating a multi language site, you probably need...

- some static pages that need to be translated
- some database driven pages that can be used for any language
- perhaps some pages are not translated yet, so you need a fallback

So your views folder might look like this:

    - views/
        |- en/
        |   |- about.blade.php (static content)
        |- nl/
        |   |- about.blade.php (static content)
        |   |- new.blade.php (not yet available in English)
        |- users/
        |   |- create.blade.php (dynamic content)
        |- not-prefixed.blade.php (not localized page)

## Localized Routing

This package will assume that your localized routes will have the 2-letter `locale` as the first URL segment: `example.com/en/about`.

The routes that you register with ViewFinder will be automatically registered with the requested locale as a prefix, but only if that locale is in your configuration array. So each locale will have the same route structure.

- example.com/en/about
- example.com/en/new
- example.com/en/users/create
- example.com/nl/about
- example.com/nl/new
- example.com/nl/users/create
- etc.

### Only Valid Locales!

Your localized routes should only be registered if the requested locale is in your `locales` array. To do this, you need to specify your routes in a closure:

    ViewFinder::routes(function()
    {
        // Your routes here!
    });

Behind the scenes this will check if the requested locale is valid and wrap the routes in a route group which adds the current locale as a prefix. So `Route::get('about', ...)` is actually `Route::get('{locale}.about', ...)` or in other words `example.com/{locale}/about`.

### Add Localized Routes

Inside the closure, you can add your routes as usual:

    ViewFinder::routes(function()
    {
        Route::get('about', [
            'as' => 'about',
            'uses' => 'HomeController@about'
        ]);
    
        Route::get('new', [
            'as' => 'new',
            'uses' => 'HomeController@new'
        ]);
    
        Route::resource('users', 'UsersController', [
            'names' => [
                // Resources get route names automatically,
                // but they will have the route prefix:
                // [en.users.create] etc.
                // Avoid the route prefix in the route names
                // by explicitly naming them...
                'create'  => 'users.create',
                'store'   => 'users.store',
                'show'    => 'users.show',
                'destroy' => 'users.destroy',
                // etc.
            ]
        ]);
    });

### Not So Localized Routes
You can also define routes outside of `ViewFinder::make()` that are not localized. Those will work like they normally do. This route (`example.com/not-prefixed`) will not be localized:

	Route::get('not-prefixed', [
  		'as' => 'not-prefixed',
   		'uses' => 'HomeController@notPrefixed'
   	]);

    ViewFinder::routes(function()
    {
        // More routes here!
    });


## Load a View

In your controller, use `ViewFinder::make` as you would Laravel's `View::make`, but exclude any locale prefixes from the view name.

    return ViewFinder::make('some.view', compact($data));
    return ViewFinder::make('some.view')->with('data', $data);

### Search Order

ViewFinder will search in the views folder in the following order:

- first it tries `some.view` (unprefixed)
- if that fails, it prepends the requested locale (ex. `nl.some.view`)
- if that fails, it tries each of the the fallback locales from your `locales` array (ex. `en.some.view`, `fr.some.view`, ...) until a match is found.

If a localized route is registered but the view can't be found, a `CodeZero\ViewFinder\ViewNotFoundException` will be thrown. If a localized route is not registered, Laravel will throw a normal 404 error.

### One Master View

If you load views in a fallback language then it is probably a good idea to let all of your pages extend one site-wide master view that you translate with [Laravel's localization](http://laravel.com/docs/5.0/localization) feature. This way, your visitors can still navigate in the requested language and only the actual content is in the unexpected language.

### Disable Fallback Locales

Maybe you don't want to present views in a fallback language to your visitors, but instead you want to catch an exception if neither the unprefixed view nor a version for the requested locale exists. Then you could redirect to a custom error page or whatever.

You can do this in two ways.

If you want to apply this to all localized routes, then you can set an options array as the first parameter of the `routes` function. The default value of `skipFallback` is `false`.

    ViewFinder::routes(['skipFallback' => true], function()
    {
        // Your routes here!
    });

But you can still overrule this when you call `ViewFinder::make()` and set this option per view. Simply add a boolean as the 4th argument:

    return ViewFinder::make('some.view', [], [], true);  // Look for fallbacks
    return ViewFinder::make('some.view', [], [], false); // Don't look for fallbacks

> The two arrays are passed to Laravel's `View::make()`.

### View Not Found

Whenever a view is missing, a `CodeZero\ViewFinder\ViewNotFoundException` will be thrown.

## Laravel's Localization

You might have views that only contain some labels and very little text. Forms for example. 

If you scroll up a bit, you'll see that our `users.create` view has a localized route (`example.com/en/users/create`) but not a localized view. ViewFinder will first check if an unprefixed version of the requested view exists and in this case it finds it and loads it.

This page can be translated with [Laravel's localization](http://laravel.com/docs/5.0/localization) feature since it doesn't have much static content (just labels etc.).

---
[![Analytics](https://ga-beacon.appspot.com/UA-58876018-1/codezero-be/viewfinder)](https://github.com/igrigorik/ga-beacon)