<?php

namespace ctbuh\Admin;

// https://docs.grafite.ca/others/form_maker/
use ctbuh\Admin\Form\HasHtmlAttributesTrait;
use Illuminate\Support\Facades\View;
use Validator;

// implements ArrayAccess - Does component specified by name exists?
class Form extends FormBase
{
    use HasHtmlAttributesTrait;

    /**
     * partial rendering
     * TODO: renderMulti
     *
     * @param $field_name
     * @return View
     */
    public function render($field_name)
    {
        return $this->model->fields()->get($field_name)->render();
    }

    /**
     *  redirect()->back()->withInput()->withErrors($price->getErrors());
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public function validator($data)
    {
        $rules = array();
        $messages = array();
        $custom_attributes = array();

        foreach ($this->fields as $field) {
            if ($field->getRules()) {
                $rules[$field->name] = $field->getRules();
            }
        }

        return Validator::make($data, $rules, $messages, $custom_attributes);
    }

    // setValues($data_model);
    public function fill($data)
    {

    }
}
