<?php

namespace ctbuh\Admin\Traits;

use ctbuh\Admin\Fields\Boolean;
use ctbuh\Admin\Fields\Date;
use ctbuh\Admin\Fields\Text;
use ctbuh\Admin\Fields\Textarea;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\HtmlString;

// https://github.com/troelskn/laravel-fillable-relations
trait CrudResourceTrait
{
    use Authorizable;

    // TODO: make Fields stateful?
    protected $fields = array();
    //protected $rules = array();

    protected $validator;

    // will transfer to /prefix/SLUG/index|edit|etc... -- defaults to: plurified slug
    //public static $route_slug; getRouteSlug();

    // NOVA: public static $model = 'App\Post';

    /**
     * Better alternative:
     * getSchema()
     * https://github.com/laravel/framework/blob/5.7/src/Illuminate/Database/Schema/Builder.php#L131
     * @return array
     */
    public static function getDatabaseColumns()
    {
        $default_connection = Config::get('database.default');
        $table_prefix = Config::get('database.connections.' . $default_connection . '.prefix');

        $instance = new static(); // create an instance of the model to be able to get the table name
        $connectionName = $instance->getConnectionName();
        $ret = DB::connection($connectionName)->select(DB::raw('SHOW COLUMNS FROM `' . $table_prefix . $instance->getTable()));

        return $ret;
    }

    /**
     * https://github.com/Laravel-Backpack/CRUD/blob/master/src/PanelTraits/Fields.p
     * Schema::guessFields();
     *
     * make this protected that way $resource->fields remain stateful
     * FielCOllection extens COllection
     * $fieldCollection->onlyIndex();
     * $fieldCOllection->onlyRelations();
     *
     * $context => $action
     * https://laravel.com/docs/5.7/controllers#resource-controllers
     *
     * @param null $context
     * @return Collection|Field[]
     */
    public function fields($context = null)
    {
        $db_fields = static::getDatabaseColumns();

        $fillable = $this->getFillable();

        // only fillable!
        if ($fillable) {
            $db_fields = array_filter($db_fields, function ($item) use (&$fillable) {
                return in_array($item->Field, $fillable);
            });
        }

        $arr = array_map(function ($field) {
            $type = $field->Type;
            $name = $field->Field;
            $default = $field->Default;

            // detect rules based on SCHEMA
            $is_required = data_get($field, 'Null') == 'NO' && data_get($field, 'Default') === null;
            $is_numeric = str_contains($type, 'int');

            $rules = array();

            if ($is_required) {
                $rules[] = 'required';
            }

            if ($is_numeric) {
                $rules[] = 'numeric';
            }

            $rules = implode('|', $rules);

            if ($type == 'text') {
                $obj = Textarea::make($name)->rules($rules);
            } elseif (str_contains($type, 'tinyint')) {
                $obj = Boolean::make($name);
            } elseif (str_contains($type, 'date') || str_contains($type, 'time')) {
                $obj = Date::make($name);
            } else {
                $obj = Text::make($name)->rules($rules)->defaultValue($default)->setValue($default);
            }

            return $obj;

        }, $db_fields);

        return collect($arr)->keyBy('name');
    }

    // https://octobercms.com/docs/api/model/form/filterfields
    public function filterFields($fields, $context)
    {
        // Do nothing
    }

    /**
     * index, create, store
     * @param string $segments,...
     * @return string
     */
    public static function getResourceBaseLink()
    {
        $slug = str_plural(snake_case(class_basename(get_called_class()), '-'));
        $custom_slug = (new static)->admin_slug;

        if (!empty($custom_slug)) {
            $slug = $custom_slug;
        }

        // $this->crud->setRouteName(config('backpack.base.route_prefix').'.article')
        $prefix = 'admin';

        if (func_num_args() > 0) {
            return sprintf('/%s/%s/%s', $prefix, $slug, implode('/', func_get_args()));
        }

        return sprintf('/%s/%s', $prefix, $slug);
    }

    public function getActionUri($action = 'index')
    {
        if ($action == 'create') {
            return static::getResourceBaseLink('create');
        } elseif (in_array($action, array('show', 'update', 'destroy'))) {
            return static::getResourceBaseLink($this->getKey());
        } elseif ($action == 'edit') {
            return static::getResourceBaseLink($this->getKey(), 'edit');
        }

        // STORE or INDEX
        return static::getResourceBaseLink();
    }

    /**
     * Implements Drupal\Core\Entity\EntityInterface::label().
     * @return string
     */
    public function label()
    {
        $names = array('name', 'email', 'first_name', 'title', 'keyword', 'topic');

        $existing = $this->getAttributes();

        // can be empty!
        $custom_first = collect($names)->first(function ($value, $key) use (&$existing) {
            return !empty($existing[$value]);
        });

        return $custom_first ? $this->{$custom_first} : class_basename($this);
    }

    public function getDisplayName()
    {
        $name = $this->label();

        if ($name) {
            return $name;
        }

        return sprintf("%s #%s", class_basename(get_called_class()), $this->getKey());
    }

    public final function getEditLink($options = null)
    {
        $str_edit = '';

        if (str_contains($options, 'edit')) {
            $str_edit = '<span class="glyphicon glyphicon-edit"></span> ';
        }

        $str = sprintf("<a href=\"%s\">%s%s</a>",
            $this->getActionUri('edit'),
            $str_edit,
            $this->getDisplayName($options)
        );

        return new HtmlString((string)$str);
    }
}
