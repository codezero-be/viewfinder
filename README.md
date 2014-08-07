# Laravel 4 Localized View Finder #

[![Build Status](https://travis-ci.org/codezero-be/viewfinder.svg?branch=master)](https://travis-ci.org/codezero-be/viewfinder)
[![Latest Stable Version](https://poser.pugx.org/codezero/viewfinder/v/stable.svg)](https://packagist.org/packages/codezero/viewfinder)
[![Total Downloads](https://poser.pugx.org/codezero/viewfinder/downloads.svg)](https://packagist.org/packages/codezero/viewfinder)
[![License](https://poser.pugx.org/codezero/viewfinder/license.svg)](https://packagist.org/packages/codezero/viewfinder)

This package provides a convenient way to automatically find a localized version of a view.

## The Problem ##

If you are creating a multi language site, you probably need...

- some static pages that need to be translated
- some database driven pages that can be used for any language
- perhaps some pages are not translated yet, so you need a fall back

So your views folder might look like this:

    - views
        - en
           - about.blade.php (static content)
        - nl
           - about.blade.php (static content)
        - users
           - create.blade.php (dynamic content)

If your URL structure looks like **http://site.com/en/about**, then you could extract the desired locale from it in your routes file (and ofcourse check if it's valid for your site):

    $locale = strtolower(Request::segment(1));

	$validLocales = [
		'en' => 'English',
        'nl' => 'Dutch',
        'fr' => 'French'
	];

	if ( ! array_key_exists($locale, $validLocales))
	{
		$locale = 'en'; // Default
	}

	App::setLocale($locale);

	// Set PHP's locale for date translations etc.
	setlocale(LC_ALL, $validLocales[$locale]);

After that you can create routes for that locale:

    Route::group(['prefix' => $locale], function() use ($locale)
    {
    	Route::get('about', [
    		'as' => 'about',
    		'uses' => 'HomeController@about'
    	]);

		Route::resource('users', 'UsersController', [
	        'names' => [
				// Avoid the route prefix in the route name...
	            'create'  => 'users.create',
	            'store'   => 'users.store',
	            'show'    => 'users.show',
	            'destroy' => 'users.destroy',
				// etc.
	        ]
		]);
    });

Every locale would end up with the same structure:

- http://site.com/en/about
- http://site.com/en/users/create
- http://site.com/nl/about
- http://site.com/nl/users/create
- ...

To return the appropriate view in the controller, you would need to find out what the current locale is and prepend it to the view (**en.**about, **nl.**about). Except for our users views, because those are just in the users folder. Getting a bit messy, no?

That's where this package comes in :)

## Installation ##

Install this package through Composer:

    "require": {
    	"codezero/viewfinder": "1.*"
    }

Next, update `app/config/app.php` to include a reference to this package's service provider in the providers array:

    'providers' => [
	    'CodeZero\ViewFinder\ViewFinderServiceProvider'
    ]
This package will automatically register the `ViewFinder` alias, if this is not already taken. You may also set it yourself, or choose another name for the alias. Just add it to the aliases array in `app/config/app.php`:

	'ViewFinder' => 'CodeZero\ViewFinder\Facade\ViewFinder'

## Usage ##

In your controller, use `ViewFinder::make` as you would Laravel's `View::make`, but exclude any locale prefixes from the view name.

The package will first see if the view exists. If not, it will prepend the current locale, set by `App::setLocale($locale);`, to the view's name and look again. If that also fails, it will grab the `fallback_locale` from `app/config/app.php`, prepend that instead and do a final lookup.

If the view still can't be found, a `CodeZero\ViewFinder\ViewNotFoundException` will be thrown.

### So in short: ###

Be sure to set the desired locale, defaults to the one set in `app/config/app.php`:

    App::setLocale($locale);

In your controllers, simply do:

	return ViewFinder::make('about');

or 

	return ViewFinder::make('users.create');

