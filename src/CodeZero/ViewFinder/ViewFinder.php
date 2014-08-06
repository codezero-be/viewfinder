<?php namespace CodeZero\ViewFinder;

interface ViewFinder {

    /**
     * Find a view or search for a localized version
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array());

    /**
     * Get the best match for a localized version of a view
     *
     * @param $view
     *
     * @return string
     * @throws ViewNotFoundException
     */
    public function getLocalizedViewName($view);

}