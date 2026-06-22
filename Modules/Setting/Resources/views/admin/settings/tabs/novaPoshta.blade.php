<div class="row">
    <div class="col-md-8">
        <h4 class="mb-3">{{ trans('setting::attributes.nova_poshta_main') }}</h4>
        {{ Form::checkbox('novaPoshta_enabled', trans('setting::attributes.novaPoshta_enabled'), trans('setting::settings.form.enable_novaPoshta'), $errors, $settings) }}
        {{ Form::text('novaPoshta_api_key', trans('setting::attributes.novaPoshta_api_key'), $errors, $settings, ['required' => true]) }}
        {{ Form::number('novaPoshta_min_amount', trans('setting::attributes.novaPoshta_min_amount'), $errors, $settings) }}

        <hr class="mt-4 mb-4">

        <h4 class="mb-3">{{ trans('setting::attributes.translatable.nova_poshta_branch_label') }}</h4>
        {{ Form::checkbox('nova_poshta_branch_enabled', trans('setting::attributes.nova_poshta_branch_enabled'), trans('setting::settings.form.enable_nova_poshta_branch'), $errors, $settings) }}
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#nova_poshta_branch{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="nova_poshta_branch{{ $locale }}">
                    {{ Form::text('translatable[nova_poshta_branch_label][' . $locale . ']', trans('setting::attributes.translatable.novaPoshta_label'), $errors, $settings, ['required' => true]) }}
                </div>
            @endforeach
        </div>
        <hr class="mt-4 mb-4">

        <h4 class="mb-3">{{ trans('setting::attributes.translatable.nova_poshta_address_label') }}</h4>
        {{ Form::checkbox('nova_poshta_address_enabled', trans('setting::attributes.nova_poshta_address_enabled'), trans('setting::settings.form.enable_nova_poshta_address'), $errors, $settings) }}
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#nova_poshta_address{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="nova_poshta_address{{ $locale }}">
                    {{ Form::text('translatable[nova_poshta_address_label][' . $locale . ']', trans('setting::attributes.translatable.novaPoshta_label'), $errors, $settings, ['required' => true]) }}
                </div>
            @endforeach
        </div>
        <hr class="mt-4 mb-4">

        <h4 class="mb-3">{{ trans('setting::attributes.translatable.nova_poshta_postomat_label') }}</h4>

        {{ Form::checkbox('nova_poshta_postomat_enabled', trans('setting::attributes.nova_poshta_postomat_enabled'), trans('setting::settings.form.enable_nova_poshta_postomat'), $errors, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#nova_poshta_postomat{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="nova_poshta_address{{ $locale }}">
                    {{ Form::text('translatable[nova_poshta_postomat_label][' . $locale . ']', trans('setting::attributes.translatable.novaPoshta_label'), $errors, $settings, ['required' => true]) }}
                </div>
            @endforeach
        </div>

        <hr class="mt-4 mb-4">

        <button type="button" class="btn btn-primary btn-sync-nova-poshta" data-bs-toggle="modal" data-bs-target="#novaPoshtaSyncModal">
            {{ trans('setting::settings.form.btn_update_np') }}
        </button>
        <p class="text-muted mt-2"><small>{{ trans('setting::settings.form.update_notice_np') }}</small></p>
    </div>
</div>

<div class="modal fade" id="novaPoshtaSyncModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('setting::settings.form.update_notice_np') }}</h5>
            </div>
            <div class="modal-body">
                <p id="sync-global-status" class="mb-3 fw-bold text-primary">{{ trans('setting::settings.form.update_init_np') }}</p>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ trans('setting::settings.form.update_area_np') }}</span>
                        <span id="text-areas" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-areas" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ trans('setting::settings.form.update_city_np') }}</span>
                        <span id="text-cities" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-cities" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ trans('setting::settings.form.update_warehouses_np') }}</span>
                        <span id="text-warehouses" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-warehouses" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="btn-close-sync-modal" class="btn btn-secondary" data-bs-dismiss="modal" disabled>{{ trans('setting::settings.form.update_close_np') }}</button>
            </div>
        </div>
    </div>
</div>
