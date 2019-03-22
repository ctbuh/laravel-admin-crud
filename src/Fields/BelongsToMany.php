<?php

namespace ctbuh\Admin\Fields;

/**
 * Class BelongsToMany
 *
 * @package ctbuh\Admin
 */
class BelongsToMany extends Select
{
    /**
     * BelongsToMany constructor.
     *
     * BelongsToMany("keywords[].keyword");
     * topic_id, Topics, topics
     *
     * @param $name
     * @param null $label
     * @param null $relation_name
     */
    public function __construct($name, $label = null, $relation_name = null)
    {
        parent::__construct($name, $label);

        $this->multiple(true);
        $this->setOptionsUsing('id:name');

        // what is the RELATION
        $relation_name = empty($relation_name) ? guess_relationship($name) : $relation_name;
        $this->setRelationship($relation_name);

        $this->formatGridValueUsing(function ($resource) use ($relation_name) {
            $models = $resource->{$relation_name};

            return collect($models)->map(function ($model) {
                return method_exists($model, 'label') ? $model->label() : data_get($model, 'id');
            });
        });
    }

    public function onLoad()
    {
        $data = $this->resource->{$this->getRelationshipName()}()->getRelated();
        $this->setOptions($data);
    }

    /**
     * BelongsToMany::withPivot(function(){
     * return fields[]
     * });
     *
     * @param $fields
     */
    public function withPivotFields($fields)
    {

    }
}
