<?php namespace CodeZero\ViewFinder; 

interface ViewFactory {

    /**
     * Make a view
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, array $data = [], array $mergeData = []);

    /**
     * Check if a view exists
     *
     * @param string $view
     *
     * @return bool
     */
    public function exists($view);

} 