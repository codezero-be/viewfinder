<?php namespace CodeZero\ViewFinder; 

interface ViewFactory {

    /**
     * Make a view
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     *
     * @return mixed
     */
    public function make($view, $data = array(), $mergeData = array());

    /**
     * Check if a view exists
     *
     * @param $view
     *
     * @return bool
     */
    public function exists($view);

} 