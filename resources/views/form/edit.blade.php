@extends('layouts.admin')

@section('content')

    <h1 class="mb-2"><i class="fa fa-edit"></i> Editing: <span class="text-success">{{ $form->modelLabel() }}</span></h1>

    @if(!empty($form->model->updated_at))
        <p class="text-muted mb-5">Last updated: {{ $form->model->updated_at->diffForHumans() }}</p>
    @endif

    <div class="row mb-5">

        <div class="col-md-4">
            <a href="{{ $form->model->getActionUri('index') }}" class="btn btn-default">
                <i class="fa fa-list-alt"></i> Back to Index
            </a>
        </div>

        <form method="POST" action="{{ $form->model->getActionUri('destroy') }}" accept-charset="UTF-8" class="form-horizontal pull-right">

            {!! csrf_field() !!}
            {!! method_field('DELETE') !!}

            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?');">
                <i class="fa fa-trash"></i> Delete
            </button>
        </form>
    </div>

    @if($errors && $errors->count() > 0)

        <div class="alert alert-danger">

            <h5>Errors</h5>

            <ul>
                @foreach($errors->all() as $message )
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    <form method="POST" action="{{ $form->model->getActionUri('update') }}" class="form-horizontal">

        {!! csrf_field() !!}
        {!! method_field('PUT') !!}

        @include('crud::form._fields', ['fields' => $form->fields()])

        <div class="form-group mt-5">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check"></i> Save Changes
                </button>
            </div>
        </div>

    </form>

@endsection
