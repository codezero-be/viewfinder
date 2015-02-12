<?php namespace CodeZero\ViewFinder\Facade;

use Illuminate\Support\Facades\Facade;

class ViewFinder extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'viewfinder';
    }

}