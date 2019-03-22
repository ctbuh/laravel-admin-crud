<div class="form-group">
    <label for="{{ $field->name }}" class="col-sm-2 control-label">{{ $field->label }}</label>
    <div class="col-sm-10">

        <textarea name="{{ $field->name }}" rows="{{ $field->getAutoRowCount() }}"
                  class="form-control {{ $field->classes() }}" {!! $field->attr() !!}>{{ $field->value }}</textarea>

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>
</div>

