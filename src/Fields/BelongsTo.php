<?php

namespace ctbuh\Admin\Fields;

/**
 * Class BelongsTo
 *
 * @package ctbuh\Admin
 */
class BelongsTo extends Select
{
    /**
     * BelongsTo constructor.
     *
     * registrant_id, Registrant, registrant_id (optional, will be guessed from name if empty)
     *
     * @param string $name
     * @param null $label
     * @param null $relation_name
     */
    public function __construct($name, $label = null, $relation_name = null)
    {
        parent::__construct($name, $label);

        // $this->placeholder = '--- Select ---';
        $this->multiple(false);

        $relation_name = $relation_name ? $relation_name : guess_relationship($name);
        $this->setRelationship($relation_name);

        $this->setOptionsUsing('id:name');

        $this->formatGridValueUsing(function ($resource) use ($relation_name) {
            $model = $resource->{$relation_name};

            return $model ? $model->label() : null;
        });
    }

    public function onLoad()
    {
        $query_model = $this->resource->{$this->getRelationshipName()}()->getRelated();
        $this->setOptions($query_model);
    }
}

