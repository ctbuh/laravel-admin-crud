@extends('layouts.admin')

@section('content')

    <h1 class="mb-5"><i class="fas fa-plus"></i> Add new <span class="text-warning">{{ $form->modelLabel() }}</span></h1>

    <div class="row mb-5">

        <div class="col-md-4">
            <a href="{{ $form->model->getActionUri('index') }}" class="btn btn-default">
                <i class="fa fa-list-alt"></i> Back to Index
            </a>
        </div>
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

    <form method="POST" action="{{ $form->model->getActionUri('store') }}" class="form-horizontal">

        {!! csrf_field() !!}

        @include('crud::form._fields', ['fields' => $form->fields()])

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-plus"></i> Add New
                </button>
            </div>
        </div>

    </form>

@endsection
