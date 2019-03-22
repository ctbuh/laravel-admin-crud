<?php

namespace ctbuh\Admin;

use App\Http\Controllers\Controller;
use ctbuh\Admin\Traits\HasCrudActions;
use Illuminate\Support\Facades\Input;

// https://stackoverflow.com/questions/50182156/laravel-reusable-resource-controller
class CrudController extends Controller
{
    use HasCrudActions;

    // TODO: registerEvents forexample when TourSession destroyed -> delete all related programs
    // TODO: setup() -> place to register resource specific logic
    // TODO: getResource() -> build resource dynamically

    // from NOVA
    // public static $model = 'App\Post';
    protected function setupLayout()
    {
        // TODO
    }

    public function __construct()
    {
        // TODO
    }

    public function restore($id)
    {
        // TODO
    }

    public function ajaxSortable()
    {
        $order = (array)Input::get('order_column');

        $ret = call_user_func($this->resource . '::setNewOrder', $order);

        return response()->make('Success!', 200);
    }

    // communicate with DataTables
    public function ajax()
    {
        // TODO
    }

}
