@php
    $isSingleValue = $option->values->count() === 1;
    $singleValue = $isSingleValue ? $option->values->first() : null;
@endphp

<div class="form-group required">
    <label class="control-label" for="option-{{ $option->id }}">
        {!! $option->name . ($option->is_required ? '<span>*</span>' : '') !!}
    </label>

    <select class="form-control"
            id="option-{{ $option->id }}"
        {{ $isSingleValue ? 'disabled' : "name=options[{$option->id}]" }}>

        @if ($option->type === 'dropdown' && !$isSingleValue)
            <option value="" selected>{{ trans('storefront::product.options.choose_an_option') }}</option>
        @endif

        @foreach ($option->values as $value)
            <option value="{{ $value->id }}" {{ $isSingleValue ? 'selected' : '' }}>
                {{ $value->label }}
            </option>
        @endforeach
    </select>

    @if($isSingleValue)
        <input type="hidden" name="options[{{ $option->id }}]" value="{{ $singleValue->id }}">
    @endif
</div>
