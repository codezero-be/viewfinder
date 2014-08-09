<?php namespace CodeZero\ViewFinder;

class ViewFinder {

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
     * View Divider
     *
     * @var string
     */
    private $divider;

    /**
     * Constructor
     *
     * @param ViewFactory $viewFactory
     * @param array $prefixes
     */
    public function __construct(ViewFactory $viewFactory, array $prefixes, $divider = '.')
    {
        $this->viewFactory = $viewFactory;
        $this->prefixes = $prefixes;
        $this->divider = $divider;
    }

    /**
     * Find and make a view
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     *
     * @return mixed
     * @throws ViewNotFoundException
     */
    public function make($view, $data = array(), $mergeData = array(), array $prefixes = null)
    {
        $view = $this->findMatchingViewName($view, $prefixes);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Get the best match for a view, with or without prefix
     *
     * @param string $view
     * @param array $prefixes
     *
     * @return string
     * @throws ViewNotFoundException
     */
    public function findMatchingViewName($view, array $prefixes = null)
    {
        $views = $this->listPossibleViewNames($view, $prefixes);

        // Loop through possible view locations
        // and return the first existing match
        foreach ($views as $v)
        {
            if ($this->viewExists($v))
            {
                return $v;
            }
        }

        // Bummer, the requested view is nowhere to be found!
        throw new ViewNotFoundException("View [$view] not found.");
    }

    /**
     * List the possible view names by prepending each prefix
     *
     * @param string $view
     * @param array $prefixes
     *
     * @return array
     */
    public function listPossibleViewNames($view, array $prefixes = null, $divider = null)
    {
        $prefixes = $prefixes ?: $this->prefixes;
        $divider = $divider ?: $this->divider;

        $views = [$view];

        foreach ($prefixes as $prefix)
        {
            $views[] = $prefix . $divider . $view;
        }

        return $views;
    }

    /**
     * Check if a view exists
     *
     * @param string $view
     *
     * @return bool
     */
    public function viewExists($view)
    {
        return $this->viewFactory->exists($view);
    }

} 