<div class="form-group">
    <label for="{{ $field->name }}" class="col-sm-2 control-label">{!! $field->label !!}</label>

    <div class="col-sm-10 col-md-5">
        <pre>{!! $field->getValue() !!}</pre>
    </div>
</div>
