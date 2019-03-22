{{--
https://symfony.com/doc/current/_images/form/form-field-parts.svg
--}}

<div class="form-group">
    <label class="control-label col-sm-2" for="{{ $field->name }}">

        @if($field->required)
            <span class="font-weight-bold text-danger">*</span>
        @endif

        {!! $field->label !!}</label>

    <div class="col-sm-10 col-md-6">

        <input type="text" name="{{ $field->name }}" value="{{ $field->getValue() }}" class="form-control {{ $field->classes() }}"
               {!! $field->attr() !!} title="">

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>
</div>



