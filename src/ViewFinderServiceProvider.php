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
        $this->registerViewFinder();
        $this->registerDefaultViewFinder();
        $this->registerViewFinderFacade();
    }

    /**
     * Register the ViewFactory binding.
     *
     * @return void
     */
    private function registerViewFactory()
    {
        $this->app->bind(
            'CodeZero\ViewFinder\ViewFactory',
            'CodeZero\ViewFinder\LaravelViewFactory'
        );
    }

    /**
     * Register the ViewFinder binding.
     *
     * @return void
     */
    private function registerViewFinder()
    {
        $this->app->bind(
            'CodeZero\ViewFinder\ViewFinder',
            'CodeZero\ViewFinder\DefaultViewFinder'
        );
    }

    /**
     * Register the ViewFinder binding.
     *
     * @return void
     */
    private function registerDefaultViewFinder()
    {
        $this->app->bind('CodeZero\ViewFinder\ViewFinder', function($app)
        {
            $prefixes = $this->getPrefixes($app);

            $viewFactory = $this->app->make('CodeZero\ViewFinder\ViewFactory');

            return new DefaultViewFinder($viewFactory, $prefixes);
        });
    }

    /**
     * Hook up the ViewFinder class with its facade key.
     *
     * @return void
     */
    private function registerViewFinderFacade()
    {
        $this->app->bindShared('viewfinder', function()
        {
            return $this->app->make('CodeZero\ViewFinder\ViewFinder');
        });
    }

    /**
     * Get the prefixes from the config file.
     *
     * @param $app
     *
     * @return array
     */
    private function getPrefixes($app)
    {
        $config = $app['config']->has("viewfinder")
            ? $app['config']->get("viewfinder")
            : include __DIR__ . '/config/config.php';

        $primaryPrefix = $config['primaryPrefix'];
        $fallbackPrefix = $config['fallbackPrefix'];
        $additionalPrefixes = $config['prefixes'];

        // Remove the current and fallback locale from the main list
        $prefixes = array_filter($additionalPrefixes, function($val) use ($primaryPrefix, $fallbackPrefix)
        {
            return $val != $primaryPrefix and $val != $fallbackPrefix;
        });

        // Put the primary and fallback locale first in the array
        array_unshift($prefixes, $primaryPrefix, $fallbackPrefix);

        return $prefixes;
    }

} 