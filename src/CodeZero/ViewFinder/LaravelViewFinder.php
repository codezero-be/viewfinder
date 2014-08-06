<?php namespace CodeZero\ViewFinder;

use Illuminate\Config\Repository as Config;
use Illuminate\View\Factory as ViewFactory;

class LaravelViewFinder implements ViewFinder {

    /**
     * Laravel Configuration
     *
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * Laravel View Factory
     *
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * Constructor
     *
     * @param ViewFactory $viewFactory
     * @param Config $config
     */
    public function __construct(ViewFactory $viewFactory, Config $config)
    {
        $this->config = $config;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Find a view or search for a localized version
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        $view = $this->getLocalizedViewName($view);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Get the best match for a localized version of a view
     *
     * @param $view
     *
     * @return string
     * @throws ViewNotFoundException
     */
    public function getLocalizedViewName($view)
    {
        $views = [
            $view,
            $this->config->get('app.locale') . '.' . $view,
            $this->config->get('app.fallback_locale') . '.' . $view
        ];

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
        throw new ViewNotFoundException("View [$view] not found.");
    }

    /**
     * Check if a view exists
     *
     * @param $view
     *
     * @return bool
     */
    private function viewExists($view)
    {
        return $this->viewFactory->exists($view);
    }

} 