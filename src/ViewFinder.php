<?php namespace CodeZero\ViewFinder;

interface ViewFinder {

    /**
     * Register Localized Routes
     *
     * @param $closure
     */
    public function routes($closure);

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
    public function make($view, array $data = [], array $mergeData = [], $divider = '.');

}