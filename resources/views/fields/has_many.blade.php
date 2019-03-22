<div class="form-group">
    <div class="col-sm-12">

        <h3 class="mb-4 text-warning"><i class="fas fa-table"></i> {{ ucwords($field->name) }}</h3>

        <div class="row mb-4">
            <div class="col-sm-6">

                <a href="{{ $field->getRelatedResourceCreateLink() }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Add new
                </a>

            </div>
        </div>

        <table class="table table-sm table-bordered" id="the_table">
            <thead>
            <tr class="bg-info">

                @foreach($field->getRelatedFields() as $sub_field)
                    <th>{{ $sub_field->name }}</th>
                @endforeach

                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($field->getValue() as $model_row)

                <tr>
                    @foreach($field->getRelatedFields() as $sub_field)

                        <td>{!! $sub_field->setValue($model_row)->gridValue() !!}</td>
                    @endforeach

                    <td width="1%">
                        <a href="{{ $model_row->getActionUri('edit') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>

    </div>
</div>

