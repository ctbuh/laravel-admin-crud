<div class="form-group">
    <label for="{{ $field->name }}" class="col-sm-2 control-label text-primary">{!! $field->label !!}:</label>

    <div class="col-sm-10 col-md-6">

        <div class="input-group">
            <span class="input-group-addon"> <i class="fas fa-clock"></i> </span>
            {{ Form::dateTimePicker($field->name, $field->getValue()) }}
        </div>

    </div>
</div>
