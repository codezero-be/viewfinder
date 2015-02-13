<?php namespace CodeZero\ViewFinder;

interface ViewFinder {

    /**
     * Register Localized Routes
     *
     * @param array|callable $options
     * @param callable $closure
     */
    public function routes($options, $closure = null);

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
     */
    public function make($view, array $data = [], array $mergeData = [], $skipFallback = false, $divider = '.');

}