<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('meest_enabled', trans('setting::attributes.meest_enabled'), trans('setting::settings.form.enable_meest'), $errors, $settings) }}
        {{ Form::text('translatable[meest_label]', trans('setting::attributes.translatable.meest_label'), $errors, $settings, ['required' => true]) }}

        {{ Form::number('meest_min_amount', trans('setting::attributes.meest_min_amount'), $errors, $settings) }}

        <hr class="mt-4 mb-4">

        <button type="button" class="btn btn-primary btn-sync-meest" data-bs-toggle="modal" data-bs-target="#meestSyncModal" disabled>
            Обновить справочную информацию Meest
        </button>

        <p class="text-muted mt-2">
            <small>Процесс может занять несколько минут. Пожалуйста, не закрывайте страницу.</small>
        </p>
    </div>
</div>

<div class="modal fade" id="meestSyncModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Обновление базы Meest</h5>
            </div>

            <div class="modal-body">
                <p id="sync-global-status" class="mb-3 fw-bold text-primary">Инициализация...</p>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Почтоматы</span>
                        <span id="text-poshtomat" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-poshtomat" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Мини-отделения</span>
                        <span id="text-minibranch" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-minibranch" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Основные отделения</span>
                        <span id="text-mainbranch" class="text-muted small">0 / 0</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div id="bar-mainbranch" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="btn-close-sync-modal" class="btn btn-secondary" data-bs-dismiss="modal" disabled>
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
