<?php

namespace ctbuh\Admin;

use ctbuh\Admin\Fields\BelongsTo;
use ctbuh\Admin\Fields\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class ResourceUpdater
 * https://github.com/code16/sharp/blob/439f565339ae5acc66b191753bd66c9dad8a040b/src/Form/Eloquent/Relationships/BelongsToRelationUpdater.php
 *
 * ----------- CRUD Operations
 *
 * TODO: make it abstract and then have EloquentResourceUpdater
 *
 * @package ctbuh\Admin
 */
final class ResourceUpdater
{
    /**
     * @param CrudResourceTrait $resource
     * @param Request $request
     */
    private static function fillAttributes($resource, $request)
    {
        $fillable = $resource->getFillable();

        // there will be cases where fillable fields would be hidden
        /*
        $fillable = $resource->fields()->filter(function (Field $field) {
            return $field->isReal() && class_basename($field) !== 'Boolean';
        })->pluck('name')->toArray();
        */

        $resource->fill($request->only($fillable));
    }

    /**
     * @param CrudResourceTrait $resource
     * @param Request $request
     */
    private static function fillBoolean($resource, $request)
    {
        // if that particular ATTRIBUTE is not present in input, set it to 0,
        // otherwise set it to whatever value that checkbox holds
        $bool = $resource->fields()->filter(function (Field $field) {
            return class_basename($field) == 'Boolean';
        })->pluck('name')->toArray();

        foreach ($bool as $name) {
            $resource->{$name} = $request->has($name);
        }
    }

    /**
     * @param CrudResourceTrait $resource
     * @param Request $request
     */
    private static function updateBelongsTo($resource, $request)
    {
        // https://github.com/code16/sharp/blob/439f565339ae5acc66b191753bd66c9dad8a040b/src/Form/Eloquent/Relationships/BelongsToRelationUpdater.php
        $resource->fields()->filter(function (Field $field) use (&$resource) {
            return ($field instanceof BelongsTo);
            // return $field->hasRelation() && class_basename($resource->{$field->getRelationMethodName()}()) == 'BelongsTo';
        })->each(function (Field $field) use (&$resource, &$request) {

            $relation = $field->getRelationshipName();
            $fk = $request->get($field->name);

            if ($fk) {
                $resource->{$relation}()->associate($fk)->save();
            } else {
                $resource->{$relation}()->dissociate()->save();
            }

        });
    }

    /**
     * @param CrudResourceTrait $resource
     * @param Request $request
     */
    private static function updateBelongsToMany($resource, $request)
    {
        $resource->fields()->filter(function (Field $field) use (&$resource) {
            return ($field instanceof BelongsToMany);
            // return $field->hasRelation() && class_basename($resource->{$field->getRelationMethodName()}()) == 'BelongsToMany';
        })->each(function (Field $field) use (&$resource, &$request) {
            $resource->{$field->getRelationshipName()}()->sync($request->get($field->name));
        });
    }

    /**
     * @param CrudResourceTrait $resource
     * @param Request $request
     */
    private static function updateHasMany($resource, $request)
    {
        // https://github.com/laravel/framework/blob/5.6/src/Illuminate/Database/Eloquent/Relations/HasOneOrMany.php#L231
        // $field->resource->{$relation}()->saveMany( requires MODELS);
    }

    /**
     *
     * https://github.com/LaravelRUS/SleepingOwlAdmin/blob/development/src/Form/FormDefault.php#L313
     * @param CrudResourceTrait $resource
     * @param Request $request
     * @return CrudResourceTrait
     */
    public static function store($resource, $request)
    {
        static::fillAttributes($resource, $request);
        static::fillBoolean($resource, $request);

        static::updateBelongsTo($resource, $request);

        $resource->save();

        static::updateBelongsToMany($resource, $request);

        return $resource;
    }

    /**
     * Update Resource Fields
     *
     * The killer feature that I'm after is a repeating field. That would be, an array of fields of a specific type, such as an array of text fields.
     * In the Wordpress plugin Advanced Custom Fields, this is called a "repeater".
     *
     * @param CrudResourceTrait $resource
     * @param Request $request
     * @return CrudResourceTrait
     */
    public static function update($resource, $request)
    {
        static::fillAttributes($resource, $request);
        static::fillBoolean($resource, $request);

        static::updateBelongsTo($resource, $request);

        $resource->save();

        static::updateBelongsToMany($resource, $request);

        return $resource;
    }

    /**
     * @param $resource
     * @param $request
     * @return bool
     */
    public static function delete($resource, $request)
    {
        $resource->delete();

        return true;
    }

}
