<div class="mb-4">
    @hasAccess('admin.options.index')
        @if ($globalOptions->isNotEmpty())
            <div class="row">
                <div class="col-9">
                    <select class="form-control" data-choices id="global-option">
                        <option value="">{{ trans('option::options.select_global_option') }}</option>

                        @foreach ($globalOptions as $globalOption)
                            <option value="{{ $globalOption->id }}">{{ $globalOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-3">
                    <button type="button" class="btn btn-secondary w-100" id="add-global-option" data-loading>
                        {{ trans('option::options.form.add_global_option') }}
                    </button>
                </div>
            </div>
        @endif
    @endHasAccess
</div>
<div class="accordion  accordion-flush" id="options-group">
    {{--  Options will be added here dynamically using JS  --}}
</div>

@push('globals')
    <script>
        Korf.data['product.options'] = {!! old_json('options', $product->options) !!};
        Korf.errors['product.options'] = @json($errors->get('options.*'), JSON_FORCE_OBJECT);
        Korf.data['get_options_link'] = "{{ route('admin.options.show', ['id' => 'REPLACE_ID']) }}";
    </script>
@endpush

@include('option::admin.options.templates.product_option')

@push('globals')
    @vite([
        'Modules/Option/Resources/assets/admin/js/main.js',
    ])
@endpush
