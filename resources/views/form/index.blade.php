@extends('layouts.admin')

@section('content-fluid')

    <h1 class="mb-5">{{ $grid->modelLabel() }} List</h1>


    <div class="row mt-5 mb-4">

        <div class="col-md-8 col-md-offset-2">

            <div class="input-group mb-3">

                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i> Search
                    </span>
                </div>

            </div>

            <input type="search" class="form-control" placeholder="Filter table... " title="Search">

        </div>

    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <a href="{{ $grid->newModel()->getActionUri('create') }}" class="btn btn-success"> <i class="fa fa-plus"></i> Add New</a>
        </div>

        <!---
        <div class="col-sm-6 text-right">
            <a href="trashed" class="btn btn-warning"><i class="fas fa-trash-restore"></i> Trashed (45)</a>
        </div>
        --->

    </div>

    <table class="table table-striped table-bordered" id="tbl_crud">
        <thead>
        <tr class="bg-info">

            @if($grid->isSortable())
                <th width="1%" nowrap="nowrap"><i class="fas fa-sort"></i> Order</th>
            @endif

            @foreach($grid->columns() as $column)
                <th>{{ $column->label }}</th>
            @endforeach

            <th width="70">Actions</th>
        </tr>
        </thead>
        <tbody>
        
        {{ $grid->newData() }}

        @foreach($grid->data() as $model)

            <tr data-model-id="{{ $model->id }}">

                @if($grid->isSortable())
                    <td>
                        {{ $model->display_order }}
                    </td>
                @endif

                @foreach($grid->columns() as $column)
                    <td>{!! $column->setValue($model)->gridValue() !!}</td>
                @endforeach

                <td>

                    <a href="{{ $model->getActionUri('edit') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>

                    @if($grid->can_delete_from_grid)

                        @if(method_exists($model, 'trashed') && $model->trashed())
                            <a href="#" class="btn btn-warning">Restore</a>
                        @else
                            <a href="javascript:void(0)" onclick="deleteEntry(this)" data-route="{{ $model->getActionUri('destroy') }}"
                               class="btn btn-xs btn-danger">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        @endif

                    @endif

                </td>
            </tr>

        @endforeach

        @if(count($grid->data()) == 0)
            <tr class="text-muted center">
                <td colspan="3">Nothing to display</td>
            </tr>
        @endif

        </tbody>
    </table>


    <script>

        var crud_table;

        var sortable = {{ $grid->isSortable() ? 'true' : 'false' }};

        var sortable_url = "ajaxSortable";
        $(function () {

            var table = $('#tbl_crud').DataTable({
                rowReorder: {
                    selector: 'td:first-child'
                },
                'paging': false,
                'info': false,
                'stateSave': false,
                'responsive': true,
                'autoWidth': false,

                "order": [],

                'dom': 'Brtip',

                //'order' : [ [1, 'desc']	],
                'fixedHeader': {
                    'header': true,
                    'headerOffset': 50
                },

            });

            crud_table = table;

            $("[type=search]").keyup(function () {

                var val = $(this).val();
                crud_table.search(val).draw();

            });

            if (sortable == false) {
                table.rowReorder.disable();
            }

            table.on('row-reordered', function (e, diff, edit) {

                var ids = $("#tbl_crud tr").map(function () {
                    return $(this).attr('data-model-id');
                }).get();

                $.post(sortable_url, {
                    order_column: ids
                }, function () {
                    console.log(arguments);
                });

            });

        });

        // https://github.com/Laravel-Backpack/CRUD/blob/1dac3a012f51ab433b0c78f45a7018d70903b061/src/resources/views/buttons/delete.blade.php
        function deleteEntry(button) {

            if (confirm('Are you sure?') === false) {
                return;
            }

            var row = $(button).closest('tr');
            var route = $(button).data('route');

            row.fadeOut(700);

            $.ajax({
                url: route,
                type: 'DELETE',
                success: function (result) {
                    row.remove();
                },
                error: function (result) {
                    row.fadeTo(0, 1);
                    alert('Error!');
                    console.log(result);
                }
            });

            return false;
        }
    </script>


@endsection
