<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;

class Date extends Field
{
    protected $view = 'date';

    public function __construct($name, $label = null)
    {
        parent::__construct($name, $label);
    }
}
