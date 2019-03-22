<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;

class Boolean extends Field
{
    protected $view = 'checkbox';

    public function __construct($name, $label = null)
    {
        parent::__construct($name, $label);

        $this->formatGridValueUsing(function ($resource) use (&$name) {
            return $resource->{$name} ? '<i class="fas fa-check"></i>' : '';
        });
    }
}
