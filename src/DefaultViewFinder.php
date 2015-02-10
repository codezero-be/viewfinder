<?php namespace CodeZero\ViewFinder;

class DefaultViewFinder implements ViewFinder {

    /**
     * View Factory
     *
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * View Prefixes
     *
     * @var array
     */
    private $prefixes;

    /**
     * Create an instance of the ViewFinder
     *
     * @param ViewFactory $viewFactory
     * @param array $prefixes
     */
    public function __construct(ViewFactory $viewFactory, array $prefixes)
    {
        $this->viewFactory = $viewFactory;
        $this->prefixes = $prefixes;
    }

    /**
     * Find and make a view
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @param array $prefixes
     * @param string $divider
     *
     * @return \Illuminate\Contracts\View\View
     * @throws ViewNotFoundException
     */
    public function make($view, array $data = [], array $mergeData = [], array $prefixes = [], $divider = '.')
    {
        $views = $this->listPossibleViewPaths($view, $prefixes, $divider);
        $view = $this->findMatchingView($views);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * List the possible view paths
     *
     * @param string $view
     * @param array $prefixes
     * @param string $divider
     *
     * @return array
     */
    private function listPossibleViewPaths($view, array $prefixes, $divider)
    {
        // Use default prefixes unless an array was passed to make()
        $prefixes = empty($prefixes) ? $this->prefixes : $prefixes;

        // Add the view without any prefixes
        $views = [$view];

        // Add the view with each prefix
        foreach ($prefixes as $prefix)
        {
            $views[] = $prefix . $divider . $view;
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
        throw new ViewNotFoundException("View [{$views[0]}] could not be found.");
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