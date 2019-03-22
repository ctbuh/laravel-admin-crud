<?php

namespace ctbuh\Admin\Traits;

use ctbuh\Admin\Field;
use ctbuh\Admin\Form;
use ctbuh\Admin\Grid;
use ctbuh\Admin\ResourceUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

/**
 * Trait HasCrudActions
 * @property string resource
 * @property array resource_with
 *
 * @package ctbuh\Admin
 */
trait HasCrudActions
{
    // protected $resource;
    // protected $resource_load = array('events', 'mailmessages');

    /**
     * must return GRID -- backpack has just setup();
     * @return Grid
     */
    protected function setupGrid()
    {
        $grid = Grid::fromResource($this->resource, null, 'index');

        if (property_exists($this, 'resource_with')) {
            $grid->query->with($this->resource_with);
        }

        return $grid;
    }

    /**
     * TODO: have to make sure whatever is passed through is fillable!
     * session() Old input combined with request() query data
     * @return array
     */
    protected function getOldInput()
    {
        // previous data!
        $request_data = request()->all();
        $old_input = session()->getOldInput();

        $prefill = array_merge($request_data, $old_input);

        return $prefill;
    }

    protected function index()
    {
        $grid = $this->setupGrid();

        return view('crud::form.index', compact('grid'));
    }

    // index/trashed/
    protected function indexTrashed()
    {
        $grid = Grid::fromResource($this->resource);

        if (method_exists('trashed', $grid->model)) {

            $grid->filter(function (Builder $query) {
                $query->onlyTrashed();
            });
        }

        return view('crud::form.index', compact('grid'));
    }

    // will have default field values by default
    protected function create()
    {
        $form = Form::create($this->resource);
        $model = $form->model;

        $model->hasAccessOrFail('create');

        $model->fill($this->getOldInput());

        $form->fields()->each(function (Field $field) use (&$model) {
            $field->setValue($model);
        });

        return view('crud::form.create', compact('form'));
    }

    private final function storeCrud(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $form = Form::create($this->resource);

        $validator = $form->validator($request->all());
        $validator->validate();

        try {
            $resource = ResourceUpdater::store($form->model, $request);
        } catch (QueryException $ex) {

            flash($ex->getMessage(), 'danger');

            return redirect()->back()->withInput();
        }

        return redirect()->to($resource->getActionUri('index'));
    }

    public function show($id)
    {
        $form = Form::fromResource($this->resource, $id);

        return view('crud::form.show', compact('form'));
    }

    public function edit($id)
    {
        $form = Form::edit($this->resource, $id);
        $model = $form->model;

        $model->hasAccessOrFail('edit');

        $model->fill($this->getOldInput());

        $form->fields()->each(function (Field $field) use (&$model) {
            $field->setValue($model);
        });

        return view('crud::form.edit', compact('form'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $form = Form::fromResource($this->resource, $id);

        $form->validator($request->all())->validate();

        try {
            $resource = ResourceUpdater::update($form->model, $request);
        } catch (QueryException $ex) {
            flash($ex->getMessage(), 'danger');

            return redirect()->back()->withInput();
        }

        return redirect()->to($resource->getActionUri('index'));
    }

    public function destroy($id)
    {
        $form = Form::fromResource($this->resource, $id);

        if ($form->model->canAction('destroy') == false) {

            if (request()->ajax()) {
                abort(403, 'Unauthorized action.');
            } else {
                die('Unauthorized action');
            }
        }

        ResourceUpdater::delete($form->model, request());

        if (request()->ajax()) {
            return 'OK!';
        }

        session()->flash('message', 'success!');

        return redirect()->to($form->model->getActionUri('index'));
    }
}
