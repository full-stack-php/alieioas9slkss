<div class="row">
    <div class="col-md-12">
        {{ Form::text('name', trans('support::export.attributes.name'), $errors, $export, ['required' => true]) }}

        {{ Form::select('entity', 'Сущность для выгрузки', $errors, $entities, $export, ['required' => true, 'class' => 'custom-select-black']) }}

        {{ Form::text('file_name', trans('support::export.attributes.file_name'), $errors, $export, ['required' => true]) }}

        {{ Form::select('format', 'Формат файла', $errors, $formats, $export, ['required' => true, 'class' => 'custom-select-black', 'id' => 'export-format-select']) }}


        <!-- НОВОЕ: Выбор языка -->
        {{ Form::select('locale', 'Язык выгрузки', $errors, $locales?? ['all' => 'Все языки', 'ru' => 'Русский', 'uk' => 'Украинский', 'en' => 'English'], $export, ['required' => true, 'class' => 'custom-select-black']) }}

        {{ Form::text('cron_schedule', 'CRON расписание', $errors, $export, ['placeholder' => 'Например: 0 0 * * * (Оставьте пустым для ручного запуска)']) }}

        {{ Form::checkbox('is_active', trans('support::export.attributes.is_active'), trans('support::export.form.enable_the_export'), $errors, $export) }}
    </div>
</div>
