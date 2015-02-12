<?php namespace CodeZero\ViewFinder;

use Illuminate\Support\ServiceProvider;

class ViewFinderServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('viewfinder.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerViewFactory();
        $this->registerLocalizer();
        $this->registerLaravelLocalizer();
        $this->registerViewFinder();
        $this->registerViewFinderFacade();
    }

    /**
     * Register the ViewFactory binding.
     *
     * @return void
     */
    private function registerViewFactory()
    {
        $this->app->singleton(
            'CodeZero\ViewFinder\ViewFactory',
            'CodeZero\ViewFinder\LaravelViewFactory'
        );
    }

    /**
     * Register the Localizer binding.
     *
     * @return void
     */
    private function registerLocalizer()
    {
        $this->app->singleton(
            'CodeZero\ViewFinder\Localizer',
            'CodeZero\ViewFinder\LaravelLocalizer'
        );
    }

    /**
     * Register the LaravelLocalizer binding.
     *
     * @return void
     */
    private function registerLaravelLocalizer()
    {
        $this->app->singleton('CodeZero\ViewFinder\LaravelLocalizer', function($app)
        {
            $config = $app['config']->has("viewfinder")
                ? $app['config']->get("viewfinder")
                : include __DIR__ . '/config/config.php';

            $locales = $config['locales'];
            $phpLocaleCategory = $config['phpLocaleCategory'];
            $translator = $app->make('Illuminate\Translation\Translator');
            $request = $app->make('Illuminate\Http\Request');;

            return new LaravelLocalizer($locales, $phpLocaleCategory, $translator, $request);
        });
    }

    /**
     * Register the ViewFinder binding.
     *
     * @return void
     */
    private function registerViewFinder()
    {
        $this->app->singleton(
            'CodeZero\ViewFinder\ViewFinder',
            'CodeZero\ViewFinder\LaravelViewFinder'
        );
    }

    /**
     * Hook up the ViewFinder class with its facade key.
     *
     * @return void
     */
    private function registerViewFinderFacade()
    {
        $this->app->singleton('viewfinder', function()
        {
            return $this->app->make('CodeZero\ViewFinder\ViewFinder');
        });
    }

} 