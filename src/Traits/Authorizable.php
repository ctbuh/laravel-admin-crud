<?php

namespace ctbuh\Admin\Traits;

// https://laravel.com/api/5.8/Illuminate/Contracts/Auth/Access/Authorizable.html
trait Authorizable
{
    // throws Exception
    public function canAction($action, $arguments = array())
    {
        return true;
    }

    public function hasAccessOrFail($action)
    {
        // TODO
    }

    // throw exception if not allowed
    public function validateAction()
    {

    }
}
