<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
            <label>
                <input type="checkbox" {{ $field->value ? 'checked="checked"' : '' }} name="{{ $field->name }}" value="1"> {{ $field->label }}
            </label>
        </div>
    </div>
</div>

