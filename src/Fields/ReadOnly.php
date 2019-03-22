<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;

/**
 * Class ReadOnly
 *
 * previous called Fake
 *
 * In addition to displaying fields that are associated with columns in your database, Nova allows you to create "computed fields".
 * Computed fields may be used to display computed values that are not associated with a database column.
 *
 * @package ctbuh\Admin\Fields
 */
class ReadOnly extends Field
{
    protected $view = 'fake';
    protected $is_fake = true;

    public $submit = false;
}
