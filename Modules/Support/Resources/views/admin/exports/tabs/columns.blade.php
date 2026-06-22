<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="columns-table">
                <thead>
                <tr>
                    <th style="width: 20%;">Название колонки (в файле)</th>
                    <th style="width: 15%;">Тип</th>
                    <th style="width: 25%;">Поле / Связь</th>
                    <th style="width: 25%;">Настройки связи</th>
                    <th class="text-center" style="width: 5%;">Вкл.</th>
                    <th class="text-center" style="width: 10%;">Действия</th>
                </tr>
                </thead>
                <tbody id="columns-container">
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6" class="text-center">
                        <button type="button" class="btn btn-default" id="add-column-btn">
                            <i class="fa fa-plus"></i> Добавить колонку
                        </button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    // Передаем сохраненные данные профиля из PHP в JS
    window.ExportSavedData = {
        columns: @json(old('columns', $export->columns ?? [])),
        filters: @json(old('filters', $export->filters ?? []))
    };

    // URL для AJAX запроса
    window.ExportEntityFieldsUrl = '{{ route('admin.exports.entity_fields') }}';
</script>
