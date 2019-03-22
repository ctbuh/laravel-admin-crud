<div class="form-group">
    <label for="{{ $field->name }}" class="col-sm-2 control-label">{{ $field->label }}</label>
    <div class="col-sm-10">

        <div class="row">

            @foreach($field->options()->chunk(3) as $chunk)

                <div class="col-sm-4">
                    @foreach($chunk as $topic_id => $topic_name)

                        <div class="checkbox">

                            <label>

                                @if($field->isSelected($topic_id))
                                    <input type="checkbox" name="{{ $field->name }}[]" checked="checked" value="{{ $topic_id }}">
                                @else
                                    <input type="checkbox" name="{{ $field->name }}[]" value="{{ $topic_id }}">
                                @endif

                                {{ $topic_name }}
                            </label>

                        </div>

                    @endforeach
                </div>

            @endforeach
        </div>

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>
</div>

