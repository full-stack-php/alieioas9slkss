<div class="form-group {!! $option->is_required ? 'required' : '' !!}">
    <label class="control-label">{!! $option->name . ($option->is_required ? '<span>*</span>' : '') !!}</label>
    <div id="input-option{{ $option->id }}">
        @foreach ($option->values as $value)
            <div class="default-radio">
                <input type="radio"
                       name="options[{{ $option->id }}]"
                       value="{{ $value->id }}"
                       data-prefix="{{ $value->price_type }}"
                       data-price="{{ $value->price }}"
                       data-prefix-special="{{ $value->special_price_type }}"
                       data-price-special="{{ $value->special_price }}"

                       {{ $loop->first ? 'checked="checked"' : '' }}
                       id="option-{{ $option->id }}-value-{{ $value->id }}" />
                <label for="option-{{ $option->id }}-value-{{ $value->id }}">
                    <span class="option-name">{{ $value->label }}</span>
                </label>
            </div>
        @endforeach
    </div>
</div>
