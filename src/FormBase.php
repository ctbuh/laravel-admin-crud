<?php

namespace ctbuh\Admin;

use App\GenericModel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

abstract class FormBase implements Arrayable
{
    // must be class resolvable
    protected $resource;

    /**
     * Form action mode, could be create|view|edit.
     *
     * @var string
     */
    protected $context = '';

    /**
     * Currently loaded model ID.
     * @var int
     */
    protected $model_id;

    /**
     * STATEFUL fields -- will double down as columns if in GRID mode
     * @var Field[]|\Illuminate\Support\Collection
     */
    protected $fields;

    /**
     * The base query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * TODO: rename this to context
     * Get the underlying model instance for the resource.
     * Will be empty (new model) for Grid context. ->exists = false
     * @var GenericModel|Model
     */
    public $model;

    /**
     * $context - The context for which the form is being defined. Can be an ORM entity, ORM resultset, array of metadata or
     * TODO: rename to model()
     * @param $resource
     * @param null $id
     * @param null $context
     * @return FormBase|Form|Grid
     */
    public static function fromResource($resource, $id = null, $context = null)
    {
        return new static($resource, $id, $context);
    }

    public static function edit($resource, $id)
    {
        return new static($resource, $id, 'edit');
    }

    public static function create($resource)
    {
        return new static($resource, null, 'create');
    }

    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Model
     */
    public function newModel()
    {
        return (new $this->resource);
    }

    private function __construct($resource, $model_id, $context = null)
    {
        $this->resource = $resource;
        $this->context = $context;
        $this->model_id = $model_id;

        $this->resolveContext();

        $this->fields = $this->model->fields($context);
    }

    // Get a fresh instance of the model represented by the resource.
    protected function resolveContext()
    {
        //$this->query()->get();
        if ($this->model_id) {
            $this->model = $this->query()->find($this->model_id);
            $this->query = $this->query();//->where('id', $this->model_id);
        } else {
            $this->model = (new $this->resource);
            $this->query = $this->query();
        }
    }

    public static function load($entity, $query_callback, $data_callback)
    {

    }

    /**
     * Aliases: builder(), newQuery()
     * Updates done through this will not trigger events
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return call_user_func($this->resource . '::query');
    }

    // TODO: getTitle() getEntityLabel()
    public function modelLabel()
    {
        $model = $this->model;

        if (method_exists($model, 'label')) {
            return $model->label();
        }

        return class_basename($this->resource);
    }

    /**
     * https://github.com/z-song/laravel-admin/blob/b6a9dc0b6874b95227cb44226f74d89e4098f95d/src/Form/Builder.php#L322
     * @return Field[]|\Illuminate\Support\Collection
     */
    public function fields()
    {
        $context = $this->context;

        return $this->fields->filter(function (Field $field) use (&$context) {
            return $field->shouldShow($context);
        });
    }

    /**
     * @param $field_name
     * @return Field
     */
    public function field($field_name)
    {
        return $this->fields->get($field_name);
    }

    // for Bootstrap3 dialog
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }
}
