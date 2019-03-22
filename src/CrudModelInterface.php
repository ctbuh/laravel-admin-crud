<?php

namespace ctbuh\Admin;

interface CrudModelInterface
{
    public function fields($request = null);

    public function canAction($action, $arguments = array());
}
