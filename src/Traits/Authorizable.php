<?php

namespace ctbuh\Admin\Traits;

// https://laravel.com/api/5.8/Illuminate/Contracts/Auth/Access/Authorizable.html
use Illuminate\Validation\UnauthorizedException;

trait Authorizable
{
    // throws Exception
    public function canAction($action, $arguments = array())
    {
        return true;
    }

    /**
     * @param $action
     */
    public function hasAccessOrFail($action)
    {
        if ($this->canAction($action) == false) {
            throw new UnauthorizedException(sprintf("Action \"%s\" not allowed on Model \"%s\"", $action, $this->label()));
        }
    }

    // throw exception if not allowed
    public function validateAction()
    {

    }
}
