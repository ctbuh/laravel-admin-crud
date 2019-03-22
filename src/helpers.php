<?php

/**
 * bs4 helper
 * https://www.yiiframework.com/doc/api/2.0/yii-helpers-basehtml#errorSummary()-detail
 */

use Illuminate\Support\Str;

if (!function_exists('id_to_name')) {

    function id_to_name($id)
    {
        return ucwords(str_replace('_', ' ', $id));
    }
}

if (!function_exists('guess_relationship')) {

    function guess_relationship($column_name)
    {
        if (ends_with($column_name, '_id')) {
            $id_less = substr($column_name, 0, strlen($column_name) - 3);

            // in case of: tour_session_id
            return Str::camel($id_less);
        }

        return $column_name;
    }
}

if (!function_exists('crud_action_name')) {

    function crud_action_name()
    {
        return 'edit';
    }
}
