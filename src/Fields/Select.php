<?php

namespace ctbuh\Admin\Fields;

use Closure;
use ctbuh\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Select
 *
 * TODO: rename Select -> List so it can be combined with HasMany too
 *
 * @package ctbuh\Admin\Fields
 */
class Select extends Field
{
    protected $view = 'select_one';

    // which one?
    protected $view_one = 'select_one';
    protected $view_multiple = 'select_multiple';

    /**
     * @var Collection|array|string
     */
    protected $options = array();

    protected $format_query = null;
    protected $options_formatting = null;

    public function __construct(string $name, $label = null)
    {
        parent::__construct($name, $label);
    }

    /**
     * Can be either a Collection, array, or Eloquent query description
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return Collection
     */
    public final function options()
    {
        $options = $this->options;

        if (is_string($options)) {
            $options = app($options)->newQuery()->get();
        } elseif ($options instanceof Model) {
            $options = $options->newQuery()->get();
        }

        // always a Collection
        if (!($options instanceof Collection)) {
            $options = collect($options);
        }

        if (is_callable($this->options_formatting)) {
            $options = call_user_func($this->options_formatting, $options);
        }

        return $options;
    }

    public function multiple($boolean)
    {
        $this->view = $boolean ? $this->view_multiple : $this->view_one;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function queryOptionsUsing($callback)
    {
        $this->format_query = $callback;
        return $this;
    }

    /**
     * @param Closure|string $callback
     * @return $this
     */
    public function setOptionsUsing($callback)
    {
        if (is_callable($callback)) {
            $this->options_formatting = $callback;
        } else {

            $parts = explode(':', $callback);

            if (count($parts) == 2) {
                $value_col = (string)$parts[0];
                $text_col = (string)$parts[1];

                $this->setOptionsUsing($this->closurePluck($value_col, $text_col));
            }
        }

        return $this;
    }

    // always be based on value
    public function isSelected($option_value)
    {
        $value = $this->getValue();

        if ($value instanceof Collection) {
            $value = $value->pluck('id')->toArray();
        } else {
            $value = array($value);
        }

        return in_array($option_value, $value);
    }

    private function closurePluck($value_col, $text_col)
    {
        /**
         * @param Collection $options
         * @return mixed
         */
        return function ($options) use ($value_col, $text_col) {
            return $options->pluck($text_col, $value_col);
        };

    }

    public function ajax($url)
    {
        // TODO
    }
}
