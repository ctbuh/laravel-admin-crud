<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;
use Illuminate\Support\Collection;

class Boolean extends Field
{
    protected $view = 'checkbox';

    public function __construct($name, $label = null)
    {
        parent::__construct($name, $label);

        $this->formatGridValueUsing(function ($value) {
            return $value ? '<i class="fas fa-check"></i>' : '';
        });
    }

}
