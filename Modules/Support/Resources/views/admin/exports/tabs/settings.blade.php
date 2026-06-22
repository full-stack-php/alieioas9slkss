<div class="row">
    <div class="col-md-12">
        <div class="format-settings-group" id="settings-csv" style="display: none;">
            <h4 class="mb-3">Настройки CSV</h4>
            @php $delimiter = $export->settings['delimiter'] ?? ';'; @endphp
            <div class="form-group">
                <label for="settings[delimiter]">Разделитель (Delimiter)</label>
                <input type="text" name="settings[delimiter]" class="form-control" value="{{ old('settings.delimiter', $delimiter) }}">
            </div>

            @php $enclosure = $export->settings['enclosure'] ?? '"'; @endphp
            <div class="form-group">
                <label for="settings[enclosure]">Ограничитель строк (Enclosure)</label>
                <input type="text" name="settings[enclosure]" class="form-control" value="{{ old('settings.enclosure', $enclosure) }}">
            </div>
        </div>

        <div class="format-settings-group" id="settings-xml" style="display: none;">
            <h4 class="mb-3">Настройки XML / YML</h4>

            @php
                $s = $export->settings ?? [];
            @endphp

            @php $xmlRootOpen = array_key_exists('xml_root_open', $s) ? $s['xml_root_open'] : "<yml_catalog date=\"{date}\">\n<shop>"; @endphp
            <div class="form-group">
                <label for="settings[xml_root_open]">Открывающий корневой тег</label>
                <textarea name="settings[xml_root_open]" class="form-control" rows="2" placeholder="<yml_catalog date=&quot;{date}&quot;> <shop>">{{ old('settings.xml_root_open', $xmlRootOpen) }}</textarea>
                <span class="help-block">Поддерживается макрос <code>{date}</code> для автоподстановки текущего времени.</span>
            </div>

            @php $xmlHeaderCode = array_key_exists('xml_header_code', $s) ? $s['xml_header_code'] : "<name>Назва магазину</name>\n<company>Назва компанії</company>\n<url>https://www.abc.ua/</url>"; @endphp
            <div class="form-group">
                <label for="settings[xml_header_code]">Произвольный код вверху фида (Информация о магазине)</label>
                <textarea name="settings[xml_header_code]" class="form-control" rows="4">{{ old('settings.xml_header_code', $xmlHeaderCode) }}</textarea>
                <span class="help-block">Эти теги будут выведены сразу после корневого тега, перед началом списка товаров.</span>
            </div>

            @php $xmlItemsWrapper = array_key_exists('xml_items_wrapper', $s) ? $s['xml_items_wrapper'] : 'offers'; @endphp
            <div class="form-group">
                <label for="settings[xml_items_wrapper]">Тег обертки списка товаров (без скобок < >)</label>
                <input type="text" name="settings[xml_items_wrapper]" class="form-control" value="{{ old('settings.xml_items_wrapper', $xmlItemsWrapper) }}" placeholder="offers">
                <span class="help-block">Например: <code>offers</code> или <code>products</code>. Оставьте пустым, если обертка не нужна.</span>
            </div>

            @php $xmlItemTag = array_key_exists('xml_item_tag', $s) ? $s['xml_item_tag'] : 'offer'; @endphp
            <div class="form-group">
                <label for="settings[xml_item_tag]">Тег самого товара (без скобок < >)</label>
                <input type="text" name="settings[xml_item_tag]" class="form-control" value="{{ old('settings.xml_item_tag', $xmlItemTag) }}" placeholder="offer">
                <span class="help-block">В этот тег будет обернут каждый отдельный товар.</span>
            </div>

            @php
                $xmlTemplate = $s['xml_template'] ?? '';
                $xmlSnippets = config('export_xml_templates');
            @endphp
            <div class="form-group">
                <label for="settings[xml_template]">Blade-шаблон элемента (Опционально)</label>

                <!-- Сюда JS будет вставлять кнопки -->
                <div id="xml-template-buttons" class="mb-2" style="display: flex; flex-wrap: wrap; gap: 5px;"></div>

                <textarea id="xml_template_textarea" name="settings[xml_template]" class="form-control" rows="10" placeholder="<id>@{{ $item->id }}</id>&#10;<name>@{{ $item->name }}</name>">{{ old('settings.xml_template', $xmlTemplate) }}</textarea>
                <span class="help-block">Доступна переменная <code>$item</code>. Кликните по кнопкам выше, чтобы вставить готовый код.</span>
            </div>

            <!-- Прокидываем шаблоны в JS -->
            <script>
                window.xmlExportSnippets = @json($xmlSnippets);
            </script>

            @php $xmlRootClose = array_key_exists('xml_root_close', $s) ? $s['xml_root_close'] : "</shop>\n</yml_catalog>"; @endphp
            <div class="form-group">
                <label for="settings[xml_root_close]">Закрывающий корневой тег</label>
                <textarea name="settings[xml_root_close]" class="form-control" rows="2" placeholder="</shop> </yml_catalog>">{{ old('settings.xml_root_close', $xmlRootClose) }}</textarea>
            </div>
        </div>

        <div class="format-settings-group" id="settings-none" style="display: none;">
            <div class="alert alert-info">Для выбранного формата дополнительные настройки не требуются.</div>
        </div>
    </div>
</div>
