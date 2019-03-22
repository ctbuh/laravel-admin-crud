<?php

namespace ctbuh\Admin\Form;

/**
 * Trait HasHtmlAttributesTrait
 *
 *  * ------------- Various Field builder helpers
 * https://github.com/z-song/laravel-admin/blob/master/src/Form/Field.php#L765
 *
 *
 * https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes
 *
 * @package ctbuh\Admin\Form
 */
trait HasHtmlAttributesTrait
{
    //protected $rules = array();
    public $required = false;
    public $disabled = false;
    public $readonly = false;
    public $placeholder = '';

    public $attributes;

    protected $classes = array();

    // jquery
    // public function data($key, $value)

    public function addClass($class_name)
    {
        $this->classes[] = $class_name;
        return $this;
    }

    /**
     * acts as empty option for select: -- select a status --
     * @param $value
     * @return $this
     */
    public function placeholder($value)
    {
        $this->placeholder = $value;
        return $this;
    }

    /**
     * @param $boolean
     * @return $this
     */
    public function required($boolean)
    {
        $this->required = $boolean;
        return $this;
    }

    /**
     * @param $boolean
     * @return $this
     */
    public function readonly($boolean)
    {
        $this->readonly = $boolean;
        return $this;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function disabled($bool)
    {
        $this->disabled = $bool;
        return $this;
    }

    public function classes()
    {
        return implode(' ', $this->classes);
    }

    // classes too!
    public function attr()
    {
        $attr = array();

        if ($this->readonly) {
            $attr['readonly'] = 'readonly';
        } elseif ($this->disabled) {
            $attr['disabled'] = 'disabled';
        } elseif ($this->required) {
            $attr['required'] = 'required';
        } elseif ($this->placeholder) {
            $attr['placeholder'] = $this->placeholder;
        }

        $output = '';

        foreach ($attr as $name => $value) {
            $output .= "{$name} = \"{$value}\"";
        }

        return $output;
    }
}
