<?php

namespace ctbuh\Admin;

use Closure;
use ctbuh\Admin\Form\HasHtmlAttributesTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Field
 *
 * https://symfony.com/doc/current/forms.html
 * https://github.com/kristijanhusak/laravel-form-builder
 *
 * basically just definitions. not supposed to be stateful YET
 *
 * // TODO: withCharCount() withWordCount()
 *
 *
 * COLUMNS
 * ->orderable(false);
 * ->searchable(false);
 * ->title() == alias for label();
 * ->visible() == alias for shouldShow('index')
 * ->width() == cell with
 * ->type() == for soring and such: model after:‌ $input_type
 * https://www.w3schools.com/tags/att_input_type.asp
 *
 * https://github.com/novius/laravel-backpack-crud-extended/blob/0.3/src/Contracts/Field.php
 *
 *
 * https://symfony.com/doc/current/_images/form/form-field-parts.svg
 *
 * @package ctbuh\Admin
 */
abstract class Field
{
    use Macroable;
    use HasHtmlAttributesTrait;

    /**
     * Required The name of the field that is submitted to the server.
     * This is also used as the source for where to read the fields data as well, unless the fields.data option is specified.
     * which corresponds with ATTRIBUTE name in the database
     *
     * @var string
     */
    public $name;

    /**
     * Default value for the field
     * Will be taken from Schema: 'unsigned int default 111';
     *
     * @var null
     */
    public $default_value;

    /**
     * Will hopefully store a REFERENCE to full row data
     *
     * @var Model
     */
    public $resource;
    public $value;

    /**
     * Will be non-empty if this field interacts with a Relationship
     * @var string
     */
    protected $relation_name;

    //protected static $all_contexts = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'restore');
    protected static $all_contexts = array('index', 'create', 'show', 'edit');
    public $visible_contexts = array('index', 'create', 'show', 'edit');

    /**
     * @var bool
     */
    protected $is_fake = false;

    protected $view = '';

    /**
     * Index - todo: rename to formatter?
     * @var Closure|string
     */
    protected $value_set_callback;

    /**
     *
     * method_name()
     * attribute_name
     * dotted.attribute_name
     * custom callback function()
     *
     * #resource_link - all belongsTo fields should show up as hyperlinks on grid
     *
     * @var Closure|string
     */
    protected $value_grid_callback;

    public $label = '';
    public $help = '';

    /**
     * Field 'name'
     *
     * will be used for fill(). Can be null for relations or fake fields
     *
     * @param string $name Attribute Name or Expression like registrant.email
     * @param null $label
     */
    public function __construct($name, $label = null)
    {
        // "name": "permission[].id", === >transformName() == permission[id][34] = 'gfg';
        $this->name = $name;
        $this->label = $label ? $label : id_to_name($name);
    }

    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     *     // TODO: ->except() ->only()
     * @param string $action,...
     * @return $this
     */
    public function showOn($action)
    {
        $args = func_get_args();
        $this->visible_contexts = $args;
        return $this;
    }


    public function hideFrom($actions)
    {
        $this->visible_contexts = array_diff($this::$all_contexts, func_get_args());
        return $this;
    }

    public function shouldShow($action)
    {
        return in_array($action, $this->visible_contexts);
    }

    /**
     * @return static
     */
    public function hideFromIndex()
    {
        return $this->hideFrom('index');
    }

    // indexOnly
    public function onlyOnIndex()
    {
        return $this->showOn('index');
    }

    public function onlyOnEdit()
    {
        return $this->showOn('edit');
    }

    public function onlyOnCreate()
    {
        return $this->showOn('create');
    }

    protected $rules;

    public function rules($rules = null, $messages = array())
    {
        $this->rules = $rules;
        return $this;
    }

    // addRule(Form::INTEGER, 'Your age must be an integer.')
    // setRequired('Please fill your name.');
    public function addRule($rule, $message)
    {
        // TODO
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function defaultValue($value)
    {
        $this->default_value = $value;
        return $this;
    }

    /**
     * TODO: move this somewhere else
     * Modeled after:
     * https://datatables.net/reference/option/columns.render#string
     *
     * resource.name -> $resource->name
     * function() -> $resource->function()
     * function_name -> $resource->function_name()
     * ANYTHING -> data_get($resource, ANYTHING)
     *
     * @param Model $resource
     * @param callable|string $format_options
     * @return mixed
     */
    public static function getValueFromResource($resource, $format_options)
    {
        if ($format_options instanceof Closure) {
            return call_user_func($format_options->bindTo(null), $resource);
        }

        // getCompanyName() -- todo: maybe allow raw method name if that method returned is not a relationship?
        if (ends_with($format_options, '()')) {
            $method_name = substr($format_options, 0, -2);

            if (method_exists($resource, $method_name)) {
                return $resource->{$method_name}();
            }
        }

        // TODO: keywords[, <-- optional, return array].keyword

        // LEGACY
        if (method_exists($resource, $format_options)) {

            $ret = $resource->{$format_options}();

            if (!($ret instanceof Relation)) {
                return $ret;
            }
        }

        // registrant.name
        return data_get($resource, $format_options);
    }

    /**
     * TODO: rename to fillFromResource
     * Resource -> data model from which to resolve this particular field.
     * This will set the options and such.
     * By default, if not given in the initialisation of the field, it is automatically set to match the value of the fields.name property.
     * https://github.com/z-song/laravel-admin/blob/master/src/Form/Field.php#L335
     *
     * @param Model|callable $resource
     * @return $this
     */
    public final function setValue($resource)
    {
        $this->resource = $resource;

        // TODO: is this even needed?
        $this->value = static::getValueFromResource($resource, !empty($this->value_set_callback) ? $this->value_set_callback : $this->name);
        $this->onLoad();

        return $this;
    }

    public function onLoad()
    {
        // DO
    }

    /**
     * Will format values on GRID too if no formatGridValueUsing is not provided.
     * @param \Closure|string $callback
     * @return $this
     */
    public function formatValueUsing($callback)
    {
        $this->value_set_callback = $callback;
        return $this;
    }

    public function getValue()
    {
        return is_null($this->value) ? $this->default_value : $this->value;
    }

    /**
     * https://github.com/z-song/demo.laravel-admin.org/blob/master/app/Admin/Controllers/PostController.php#L173
     * if an array -> render as table?
     * @param bool $as_string
     * @return string|Renderable
     */
    public function gridValue($as_string = true)
    {
        $formatter = $this->name;

        if (!empty($this->value_grid_callback)) {
            $formatter = $this->value_grid_callback;
        } elseif (!empty($this->value_set_callback)) {
            $formatter = $this->value_set_callback;
        }

        $val = $this::getValueFromResource($this->resource, $formatter);

        return $as_string ? (is_array($val) ? collect($val) : $val) : $val;
    }

    /**
     * TODO: maybe rename transform() to use as much laravel language as possible
     * @param \Closure|string $callback
     * @return $this
     */
    public function formatGridValueUsing($callback)
    {
        $this->value_grid_callback = $callback;
        return $this;
    }

    /**
     * Does this field interact with a Relationship?
     * @return bool
     */
    public function isRelationship()
    {
        return !empty($this->relation_name);
    }

    public function setRelationship($relation)
    {
        $this->relation_name = $relation;
        return $this;
    }

    public function getRelationshipName()
    {
        return $this->relation_name;
    }

    public function isFake()
    {
        return $this->is_fake;
    }

    public function fake()
    {
        $this->is_fake = 1;
        return $this;
    }

    /**
     * @param $label
     * @return Field
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * helpAbove() helpBelow()
     * @param $help
     * @return $this
     */
    public function help($help)
    {
        $this->help = $help;
        return $this;
    }

    // TODO
    public function helpAsLink($text, $link)
    {
        return $this;
    }

    /**
     * https://github.com/z-song/laravel-admin/blob/master/src/Form/Field.php#L1061
     * @return string
     */
    public function getView()
    {
        $view = view()->make('crud::fields.' . $this->view, array(
            'field' => $this,
            'text' => mt_rand(1, 50000)
        ));

        return $view;

        // custom fields
        if (view()->exists($this->view)) {
            return $this->view;
        }

        return 'crud::fields.' . $this->view;
    }


    /**
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
       // $something = clone $this;

        return $this->getView();
    }

    // --- DEV usage only!!!!
    protected static $cache = array();

    /**
     * @param \Closure $callback
     * @param $key
     * @return mixed
     */
    protected function once($callback, $key)
    {
        if (!isset(static::$cache[$key])) {
            $callback->bindTo($this);

            static::$cache[$key] = call_user_func($callback);
        }

        return static::$cache[$key];
    }
}
