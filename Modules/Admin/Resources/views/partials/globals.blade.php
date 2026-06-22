<script>
    window.Korf = {
        version: '{{ korf_version() }}',
        csrfToken: '{{ csrf_token() }}',
        baseUrl: '{{ trim(localized_url(locale(), url('/')),'/') }}',
        rtl: {{ is_rtl() ? 'true' : 'false' }},
        locale: '{{ locale() }}',
        supportedLocales: @json(supported_locales()),
        langs: {},
        data: {},
        errors: {},
        selectize: [],
        defaultCurrencySymbol: '{{ currency_symbol(setting("default_currency")) }}'
    };

    Korf.langs['admin::admin.buttons.create'] = '{{ trans('admin::admin.buttons.create') }}';
    Korf.langs['admin::admin.buttons.delete'] = '{{ trans('admin::admin.buttons.delete') }}';
    Korf.langs['media::media.file_manager.title'] = '{{ trans('media::media.file_manager.title') }}';
    Korf.langs['admin::admin.table.search_here'] = '{{ trans('admin::admin.table.search_here') }}';
    Korf.langs['admin::admin.table.showing_start_end_total_entries'] = '{{ trans('admin::admin.table.showing_start_end_total_entries') }}';
    Korf.langs['admin::admin.table.showing_empty_entries'] = '{{ trans('admin::admin.table.showing_empty_entries') }}';
    Korf.langs['admin::admin.table.show_menu_entries'] = '{{ trans('admin::admin.table.show_menu_entries') }}';
    Korf.langs['admin::admin.table.filtered_from_max_total_entries'] = '{{ trans('admin::admin.table.filtered_from_max_total_entries') }}';
    Korf.langs['admin::admin.table.no_data_available_table'] = '{{ trans('admin::admin.table.no_data_available_table') }}';
    Korf.langs['admin::admin.table.loading'] = '{{ trans('admin::admin.table.loading') }}';
    Korf.langs['admin::admin.table.no_matching_records_found'] = '{{ trans('admin::admin.table.no_matching_records_found') }}';
    Korf.langs['core::messages.something_went_wrong'] = '{{ trans('core::messages.something_went_wrong') }}';
</script>

@stack('globals')
