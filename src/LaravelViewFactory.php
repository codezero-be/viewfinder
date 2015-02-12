<?php namespace CodeZero\ViewFinder;

use Illuminate\Contracts\View\Factory as IlluminateViewFactory;

class LaravelViewFactory implements ViewFactory {

    /**
     * Laravel View Factory
     *
     * @var IlluminateViewFactory
     */
    private $viewFactory;

    /**
     * Constructor
     *
     * @param IlluminateViewFactory $viewFactory
     */
    public function __construct(IlluminateViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Make a view
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, array $data = [], array $mergeData = [])
    {
        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Check if a view exists
     *
     * @param string $view
     *
     * @return bool
     */
    public function exists($view)
    {
        return $this->viewFactory->exists($view);
    }

} 