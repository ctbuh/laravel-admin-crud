<div class="form-group">
    <label for="{{ $field->name }}" class="col-sm-2 control-label text-warning">{{ $field->label }}</label>
    <div class="col-sm-10">

        <select name="{{ $field->name }}" class="form-control">

            @if($field->placeholder)
                <option value="">{{ $field->placeholder }}</option>
            @endif

            @foreach($field->options() as $key => $value)

                @if($field->isSelected($key))
                    <option value="{{ $key }}" selected="selected">{{ $value }}</option>
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif

            @endforeach
        </select>

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>
</div>




