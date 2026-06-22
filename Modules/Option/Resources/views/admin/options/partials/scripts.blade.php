@push('globals')
    <script>
        Korf.data['supported_locales'] = @json(array_keys(supported_locales()));
        Korf.data['option.values'] = {!! old_json('values', $option->values ?? []) !!};
        Korf.data['errors'] = @json($errors->toArray());
    </script>
@endpush

@push('scripts')

@endpush
