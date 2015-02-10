<?php namespace CodeZero\ViewFinder;

interface ViewFinder {

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
    public function make($view, array $data = [], array $mergeData = [], array $prefixes = [], $divider = '.');

}