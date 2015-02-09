<?php namespace CodeZero\ViewFinder;

use Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ViewFinderServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('codezero/viewfinder');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerViewFactory();

        $this->registerViewFinder();

        $this->registerViewFinderFacade();

        $this->registerViewFinderAlias();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['viewfinder'];
    }

    /**
     * Register the ViewFactory binding
     */
    private function registerViewFactory()
    {
        $this->app->bind(
            'CodeZero\ViewFinder\ViewFactory',
            'CodeZero\ViewFinder\LaravelViewFactory'
        );
    }

    /**
     * Register the ViewFinder binding
     */
    private function registerViewFinder()
    {
        $this->app->bind('CodeZero\ViewFinder\ViewFinder', function($app)
        {
            $divider = $app['config']->get('viewfinder::config.divider');
            $prefixes = $this->getPrefixes($app);

            $viewFactory = $this->app->make('CodeZero\ViewFinder\ViewFactory');

            return new ViewFinder($viewFactory, $prefixes, $divider);
        });
    }

    /**
     * Hook up the ViewFinder class for the ViewFinder alias
     */
    private function registerViewFinderFacade()
    {
        $this->app->bindShared('viewfinder', function()
        {
            return $this->app->make('CodeZero\ViewFinder\ViewFinder');
        });
    }

    /**
     * Register the ViewFinder alias if it does not already exist
     */
    private function registerViewFinderAlias()
    {
        $this->app->booting(function()
        {
            $aliases = Config::get('app.aliases');

            if (empty($aliases['ViewFinder']))
            {
                AliasLoader::getInstance()->alias(
                    'ViewFinder',
                    'CodeZero\ViewFinder\Facade\ViewFinder'
                );
            }
        });
    }

    /**
     * Get the prefixes from the config file
     */
    private function getPrefixes($app)
    {
        $config = $app['config'];

        // Get the prefixes with highest priority
        $primary = $config->get('viewfinder::config.primary');
        $fallback = $config->get('viewfinder::config.fallback');

        // Get a list of all prefixes
        $prefixes = $config->get('viewfinder::config.prefixes');

        // Remove the current and fallback locale from the main list
        $prefixes = array_filter($prefixes, function($val) use ($primary, $fallback)
        {
            return $val != $primary and $val != $fallback;
        });

        // Put the primary and fallback locale first in the array
        array_unshift($prefixes, $primary, $fallback);

        return $prefixes;
    }

} 