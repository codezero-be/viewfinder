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
     * Register the ViewFinder binding
     */
    private function registerViewFinder()
    {
        $this->app->bind(
            'CodeZero\ViewFinder\ViewFinder',
            'CodeZero\ViewFinder\LaravelViewFinder'
        );
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
            $loader = AliasLoader::getInstance();
            $aliases = Config::get('app.aliases');

            if (empty($aliases['ViewFinder']))
            {
                $loader->alias('ViewFinder', 'CodeZero\ViewFinder\Facade\ViewFinder');
            }
        });
    }

} 