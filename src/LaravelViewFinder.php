<?php namespace CodeZero\ViewFinder;

use Illuminate\Contracts\Routing\Registrar as Router;

class LaravelViewFinder implements ViewFinder {

    /**
     * View Factory
     *
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * Localizer
     *
     * @var Localizer
     */
    private $localizer;

    /**
     * Laravel's Router
     *
     * @var Router
     */
    private $router;

    /**
     * Create an instance of the ViewFinder
     *
     * @param ViewFactory $viewFactory
     * @param Localizer $localizer
     * @param Router $router
     */
    public function __construct(ViewFactory $viewFactory, Localizer $localizer, Router $router)
    {
        $this->viewFactory = $viewFactory;
        $this->localizer = $localizer;
        $this->router = $router;
    }

    /**
     * Register Localized Routes
     *
     * @param $closure
     */
    public function routes($closure)
    {
        if ($this->localizer->isRequestedLocaleValid())
        {
            $this->router->group(['prefix' => $this->localizer->getLocale()], function() use ($closure) {
                $closure();
            });
        }
    }

    /**
     * Find and make a view
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @param string $divider
     *
     * @return \Illuminate\Contracts\View\View
     * @throws ViewNotFoundException
     */
    public function make($view, array $data = [], array $mergeData = [], $divider = '.')
    {
        $views = $this->listPossibleViewPaths($view, $divider);
        $view = $this->findMatchingView($views);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * List the possible view paths
     *
     * @param string $view
     * @param string $divider
     *
     * @return array
     */
    private function listPossibleViewPaths($view, $divider)
    {
        // Get the locale array keys (not the value, which is the PHP locale)
        $locales = array_keys($this->localizer->getLocales());

        // Get the requested locale
        $requestedLocale = $this->localizer->getRequestedLocale();

        // Add the view without any prefixes
        $views = [$view];

        if (in_array($requestedLocale, $locales))
        {
            // Add the view with each prefix
            foreach ($locales as $locale)
            {
                $views[] = $locale . $divider . $view;
            }
        }

        return $views;
    }

    /**
     * Loop through the views and return the first one that exists
     *
     * @param array $views
     *
     * @return string
     * @throws ViewNotFoundException
     */
    private function findMatchingView(array $views)
    {
        // Loop through possible view locations
        // and return the first existing match
        foreach ($views as $view)
        {
            if ($this->viewExists($view))
            {
                return $view;
            }
        }

        // Bummer, the requested view is nowhere to be found!
        throw new ViewNotFoundException("Route exists but the view [{$views[0]}] could not be found.");
    }

    /**
     * Check if a view exists
     *
     * @param string $view
     *
     * @return bool
     */
    private function viewExists($view)
    {
        return $this->viewFactory->exists($view);
    }

} 