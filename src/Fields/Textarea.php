<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;

class Textarea extends Field
{
    protected $view = 'textarea';

    public function getAutoRowCount()
    {
        $lc = substr_count($this->getValue(), "\n" );
        $len = strlen($this->getValue());

        return max(4, ceil($len / 80));
    }
}
