<?php namespace CodeZero\ViewFinder;

use Illuminate\View\Factory as IlluminateViewFactory;

class LaravelViewFactory implements ViewFactory {

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
     */
    public function __construct(IlluminateViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Make a view
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Check if a view exists
     *
     * @param $view
     *
     * @return bool
     */
    public function exists($view)
    {
        return $this->viewFactory->exists($view);
    }

} 