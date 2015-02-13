<?php namespace CodeZero\ViewFinder;

use Illuminate\Contracts\Routing\Registrar as Router;
use InvalidArgumentException;

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
     * Check for fallback views?
     *
     * @var bool
     */
    private $skipFallback = false;

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
     * @param array|callable $options
     * @param callable $closure
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function routes($options, $closure = null)
    {
        $closure = $this->findRoutesClosure($options, $closure);

        $this->setRoutesOptions($options);

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
     * @param bool $skipFallback
     * @param string $divider
     *
     * @return \Illuminate\Contracts\View\View
     * @throws ViewNotFoundException
     */
    public function make($view, array $data = [], array $mergeData = [], $skipFallback = null, $divider = '.')
    {
        if ($skipFallback === null)
        {
            $skipFallback = $this->skipFallback;
        }

        $views = $this->listPossibleViewPaths($view, $skipFallback, $divider);

        $view = $this->findMatchingView($views);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * List the possible view paths
     *
     * @param string $view
     * @param bool $skipFallback
     * @param string $divider
     *
     * @return array
     */
    private function listPossibleViewPaths($view, $skipFallback, $divider)
    {
        $views = [];

        if ($this->localizer->isRequestedLocaleValid())
        {
            $views = $skipFallback
                ? [$this->getRequestedViewPath($view, $divider)]
                : $this->getAllViewPaths($view, $divider);
        }

        // Add the view without any prefixes
        // to the top of the array
        array_unshift($views, $view);

        return $views;
    }

    /**
     * Get a specific view path based on a locale
     *
     * @param string $locale
     * @param string $view
     * @param string $divider
     *
     * @return string
     */
    private function getViewPath($locale, $view, $divider)
    {
        return $locale . $divider . $view;
    }


    /**
     * Get the view path for the requested locale
     *
     * @param string $view
     * @param string $divider
     *
     * @return string
     */
    private function getRequestedViewPath($view, $divider)
    {
        // Get the requested locale
        $locale = $this->localizer->getRequestedLocale();

        return $this->getViewPath($locale, $view, $divider);
    }

    /**
     * Get an array of all possible view paths
     *
     * @param string $view
     * @param string $divider
     *
     * @return array
     */
    private function getAllViewPaths($view, $divider)
    {
        // Get the locale array keys (not the value, which is the PHP locale)
        $locales = array_keys($this->localizer->getLocales());

        $views = [];

        // Add the view with each prefix
        foreach ($locales as $locale)
        {
            $views[] = $this->getViewPath($locale, $view, $divider);
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

    /**
     * Check which argument is the closure
     *
     * @param $options
     * @param $closure
     *
     * @return callable
     * @throws InvalidArgumentException
     */
    private function findRoutesClosure($options, $closure)
    {
        if (is_callable($options))
        {
            return $options;
        }
        elseif (is_callable($closure))
        {
            return $closure;
        }

        throw new InvalidArgumentException('A closure must be provided as the first or second argument.');
    }

    /**
     * Set routes options if provided
     *
     * @param $options
     *
     * @return void
     */
    private function setRoutesOptions($options)
    {
        if (is_array($options) and array_key_exists('skipFallback', $options))
        {
            $this->skipFallback = $options['skipFallback'];
        }
    }

} 