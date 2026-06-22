document.addEventListener('DOMContentLoaded', async function () {
    console.log('DOMContentLoaded: Инициализация модуля экспорта');

    // --- 1. Управление форматами (Choices.js фикс) ---
    const formatSelect = document.getElementById('format') || document.querySelector('select[name="format"]');
    const settingsGroups = document.querySelectorAll('.format-settings-group');

    function toggleSettingsVisibility() {
        if (!formatSelect) return;
        const selectedFormat = formatSelect.value.toLowerCase();

        settingsGroups.forEach(group => group.style.display = 'none');

        const targetGroup = document.getElementById(`settings-${selectedFormat}`);
        if (targetGroup) {
            targetGroup.style.display = 'block';
        } else {
            const noneGroup = document.getElementById('settings-none');
            if (noneGroup) noneGroup.style.display = 'block';
        }
    }

    if (formatSelect) {
        formatSelect.addEventListener('change', toggleSettingsVisibility);
        formatSelect.addEventListener('choice', toggleSettingsVisibility);
        setTimeout(toggleSettingsVisibility, 100);
    }

    // --- 2. Загрузка полей сущности (AJAX) ---
    let availableData = { fields: {}, relations: {} };
    const entitySelect = document.querySelector('select[name="entity"]');

    async function loadEntityFields(entityClass) {
        if (!entityClass || !window.ExportEntityFieldsUrl) return;
        try {
            const response = await fetch(`${window.ExportEntityFieldsUrl}?entity=${encodeURIComponent(entityClass)}`);
            availableData = await response.json();
        } catch (error) {
            console.error('Ошибка загрузки полей сущности:', error);
        }
    }

    // --- 3. Конструктор колонок ---
    const columnsContainer = document.getElementById('columns-container');
    const addColumnBtn = document.getElementById('add-column-btn');
    let columnCount = 0;

    function addColumnRow(data = {}) {
        const index = columnCount++;

        // Читаем сохраненные данные (если есть)
        const type = data.type || 'field';
        const columnName = data.column || '';
        const selectedTarget = data.field || '';
        // relation_fields может быть массивом сохраненных полей связи
        const selectedRelationFields = Array.isArray(data.relation_fields) ? data.relation_fields : (data.relation_fields ? Object.values(data.relation_fields) : []);
        const isExplode = data.explode == 1;
        const isEnabled = data.hasOwnProperty('enabled') ? (data.enabled == 1) : true;

        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>
                <input type="text" name="columns[${index}][column]" class="form-control" value="${columnName}" placeholder="Название в файле" required>
            </td>
            <td>
                <select name="columns[${index}][type]" class="form-control column-type-select">
                    <option value="field" ${type === 'field' ? 'selected' : ''}>Обычное поле</option>
                    <option value="relation" ${type === 'relation' ? 'selected' : ''}>Связь (Relation)</option>
                </select>
            </td>
            <td class="target-container">
                <!-- Select для выбора поля или связи рендерится через JS -->
            </td>
            <td class="relation-fields-container" style="background: #fdfdfd; border: 1px solid #eee;">
                <!-- Настройки колонки (чекбоксы, теги) рендерятся здесь -->
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <input type="hidden" name="columns[${index}][enabled]" value="0">
                <input type="checkbox" name="columns[${index}][enabled]" value="1" ${isEnabled ? 'checked' : ''}>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <button type="button" class="btn btn-soft-danger btn-sm remove-row-btn" title="Удалить"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
            </td>
        `;

        const typeSelect = tr.querySelector('.column-type-select');
        const targetContainer = tr.querySelector('.target-container');
        const relationContainer = tr.querySelector('.relation-fields-container');

        // Функция: Рендерим select (Поле или Связь)
        function renderTargetSelect() {
            const currentType = typeSelect.value;
            let targetHtml = `<select name="columns[${index}][field]" class="form-control target-select" required>`;

            if (currentType === 'field') {
                targetHtml += '<option value="">-- Выберите поле --</option>';
                for (const [key, label] of Object.entries(availableData.fields || {})) {
                    targetHtml += `<option value="${key}" ${key === selectedTarget ? 'selected' : ''}>${label}</option>`;
                }
            } else if (currentType === 'relation') {
                targetHtml += '<option value="">-- Выберите связь --</option>';
                for (const [key, relData] of Object.entries(availableData.relations || {})) {
                    targetHtml += `<option value="${key}" ${key === selectedTarget ? 'selected' : ''}>${relData.label}</option>`;
                }
            }

            targetHtml += '</select>';
            targetContainer.innerHTML = targetHtml;

            // Вешаем слушатель на выбор конкретного поля/связи
            const targetSelect = targetContainer.querySelector('.target-select');
            targetSelect.addEventListener('change', renderSettings);
            renderSettings.call(targetSelect); // Вызываем сразу для отрисовки настроек
        }

        // Функция: Универсальный рендер настроек (Для полей и связей)
        function renderSettings() {
            const currentType = typeSelect.value;
            const targetSelect = targetContainer.querySelector('.target-select');
            const selectedField = targetSelect ? targetSelect.value : '';

            // --- Настройки для СВЯЗЕЙ ---
            if (currentType === 'relation') {
                if (!selectedField || !availableData.relations[selectedField]) {
                    relationContainer.innerHTML = '<span class="text-muted d-block p-2 text-center">Выберите связь</span>';
                    return;
                }

                const relData = availableData.relations[selectedField];
                const savedSeparator = data.separator || '\\n';
                let html = '<div class="p-2">';

                for (const [fieldKey, fieldLabel] of Object.entries(relData.fields)) {
                    const isChecked = selectedRelationFields.includes(fieldKey) ? 'checked' : '';
                    html += `
                        <div class="checkbox mb-1">
                            <label>
                                <input type="checkbox" name="columns[${index}][relation_fields][]" value="${fieldKey}" ${isChecked}>
                                ${fieldLabel}
                            </label>
                        </div>`;
                }

                html += `
                    <hr class="mt-2 mb-2">
                    <div class="form-group mb-2">
                        <label class="text-muted" style="font-size: 11px; text-transform: uppercase;">Разделитель записей:</label>
                        <select name="columns[${index}][separator]" class="form-control" style="height: 30px; font-size: 12px; padding: 2px 10px;">
                            <option value="\\n" ${savedSeparator === '\\n' ? 'selected' : ''}>С новой строки (Alt+Enter)</option>
                            <option value="," ${savedSeparator === ',' ? 'selected' : ''}>Запятая (,)</option>
                            <option value=";" ${savedSeparator === ';' ? 'selected' : ''}>Точка с запятой (;)</option>
                            <option value="|" ${savedSeparator === '|' ? 'selected' : ''}>Прямая черта (|)</option>
                            <option value=" " ${savedSeparator === ' ' ? 'selected' : ''}>Пробел</option>
                        </select>
                    </div>
                `;

                if (relData.can_explode && selectedField !== 'bundles') {
                    html += `
                        <div class="checkbox text-danger mt-2">
                            <label title="Создаст отдельные строки в файле для каждой записи этой связи">
                                <input type="hidden" name="columns[${index}][explode]" value="0">
                                <input type="checkbox" name="columns[${index}][explode]" value="1" ${isExplode ? 'checked' : ''}>
                                <strong>Выводить отдельными строками</strong>
                            </label>
                        </div>`;
                }

                html += '</div>';
                relationContainer.innerHTML = html;
            }
            // --- Настройки для ОБЫЧНЫХ ПОЛЕЙ ---
            else if (currentType === 'field') {
                if (selectedField === 'description' || selectedField === 'short_description') {
                    const isStripTags = data.strip_tags == '1' ? 'checked' : '';
                    relationContainer.innerHTML = `
                        <div class="p-2" style="background: #fdfdfd; border: 1px solid #eee;">
                            <div class="checkbox mb-0">
                                <label>
                                    <input type="hidden" name="columns[${index}][strip_tags]" value="0">
                                    <input type="checkbox" name="columns[${index}][strip_tags]" value="1" ${isStripTags}>
                                    <strong>Убрать HTML-теги</strong>
                                </label>
                            </div>
                        </div>
                    `;
                } else {
                    relationContainer.innerHTML = '<span class="text-muted d-block p-2 text-center">Нет дополнительных настроек</span>';
                }
            }
        }

        // Привязываем события
        typeSelect.addEventListener('change', renderTargetSelect);
        tr.querySelector('.remove-row-btn').addEventListener('click', () => tr.remove());

        // Первичный рендер при добавлении строки
        renderTargetSelect();
        columnsContainer.appendChild(tr);
    }


    // --- 4. Конструктор фильтров ---
    const filtersContainer = document.getElementById('filters-container');
    const addFilterBtn = document.getElementById('add-filter-btn');
    let filterCount = 0;
    const operators = ['=', '!=', 'LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NULL', 'NOT NULL'];

    function addFilterRow(data = {}) {
        if (!filtersContainer) return;
        const index = filterCount++;
        const selectedField = data.field || '';
        const operator = data.operator || '=';
        const value = data.value || '';

        // Генерируем Select с группировкой полей и связей
        let fieldSelectHtml = `<select name="filters[${index}][field]" class="form-control" required>`;
        fieldSelectHtml += '<option value="">-- Выберите поле --</option>';

        // 1. Обычные поля
        if (availableData.fields && Object.keys(availableData.fields).length > 0) {
            fieldSelectHtml += '<optgroup label="Обычные поля">';
            for (const [key, label] of Object.entries(availableData.fields)) {
                fieldSelectHtml += `<option value="${key}" ${key === selectedField ? 'selected' : ''}>${label}</option>`;
            }
            fieldSelectHtml += '</optgroup>';
        }

        // 2. Поля связей
        if (availableData.relations && Object.keys(availableData.relations).length > 0) {
            for (const [relKey, relData] of Object.entries(availableData.relations)) {
                fieldSelectHtml += `<optgroup label="${relData.label}">`;
                for (const [subKey, subLabel] of Object.entries(relData.fields)) {
                    const fullFieldKey = `${relKey}.${subKey}`;
                    fieldSelectHtml += `<option value="${fullFieldKey}" ${fullFieldKey === selectedField ? 'selected' : ''}>${subLabel}</option>`;
                }
                fieldSelectHtml += '</optgroup>';
            }
        }
        fieldSelectHtml += '</select>';

        let opsHtml = '';
        operators.forEach(op => {
            opsHtml += `<option value="${op}" ${operator === op ? 'selected' : ''}>${op}</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${fieldSelectHtml}</td>
            <td>
                <select name="filters[${index}][operator]" class="form-control operator-select">
                    ${opsHtml}
                </select>
            </td>
            <td>
                <input type="text" name="filters[${index}][value]" class="form-control filter-value-input" value="${value}" placeholder="Значение" ${['NULL', 'NOT NULL'].includes(operator) ? 'readonly' : 'required'}>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <button type="button" class="btn btn-soft-danger btn-sm remove-row-btn"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
            </td>
        `;

        const operatorSelect = tr.querySelector('.operator-select');
        const valueInput = tr.querySelector('.filter-value-input');

        operatorSelect.addEventListener('change', function() {
            if (['NULL', 'NOT NULL'].includes(this.value)) {
                valueInput.setAttribute('readonly', 'readonly');
                valueInput.removeAttribute('required');
                valueInput.value = '';
            } else {
                valueInput.removeAttribute('readonly');
                valueInput.setAttribute('required', 'required');
            }
        });

        tr.querySelector('.remove-row-btn').addEventListener('click', () => tr.remove());
        filtersContainer.appendChild(tr);
    }

    // --- 5. Конструктор XML-шаблона (НОВОЕ) ---
    function insertAtCaret(areaId, text) {
        const txtarea = document.getElementById(areaId);
        if (!txtarea) return;

        const scrollPos = txtarea.scrollTop;
        let strPos = 0;
        const br = ((txtarea.selectionStart || txtarea.selectionStart === '0') ? "ff" : (document.selection ? "ie" : false));

        if (br === "ie") {
            txtarea.focus();
            const range = document.selection.createRange();
            range.moveStart('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if (br === "ff") {
            strPos = txtarea.selectionStart;
        }

        const front = (txtarea.value).substring(0, strPos);
        const back = (txtarea.value).substring(strPos, txtarea.value.length);

        txtarea.value = front + text + back;
        strPos = strPos + text.length;

        if (br === "ie") {
            txtarea.focus();
            const ieRange = document.selection.createRange();
            ieRange.moveStart('character', -txtarea.value.length);
            ieRange.moveStart('character', strPos);
            ieRange.moveEnd('character', 0);
            ieRange.select();
        } else if (br === "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }

        txtarea.scrollTop = scrollPos;
    }

    function renderXmlTemplateButtons() {
        const container = document.getElementById('xml-template-buttons');
        if (!container) return;

        container.innerHTML = '';
        const snippets = window.xmlExportSnippets || { fields: {}, relations: {}, default: '<{field}>{{ $item->{field} }}</{field}>\n' };
        const selectedFields = [];

        // Собираем данные из селектов
        if (columnsContainer) {
            columnsContainer.querySelectorAll('.target-select').forEach(select => {
                if (select.value) selectedFields.push(select.value);
            });
        }

        const uniqueFields = [...new Set(selectedFields)];

        uniqueFields.forEach(field => {
            let snippetText = snippets.fields?.[field] || snippets.relations?.[field];
            if (!snippetText) {
                snippetText = snippets.default.replace(/{field}/g, field);
            }

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary';
            btn.innerText = '+' + field;
            btn.title = 'Вставить шаблон для ' + field;

            btn.onclick = function(e) {
                e.preventDefault();
                insertAtCaret('xml_template_textarea', snippetText);
            };

            container.appendChild(btn);
        });
    }


    // --- 6. Запуск скрипта и инициализация событий ---
    if (addColumnBtn) addColumnBtn.addEventListener('click', () => addColumnRow());
    if (addFilterBtn) addFilterBtn.addEventListener('click', () => addFilterRow());

    // Загружаем данные с сервера
    if (entitySelect) {
        await loadEntityFields(entitySelect.value);

        entitySelect.addEventListener('change', async (e) => {
            await loadEntityFields(e.target.value);
            if (columnsContainer) columnsContainer.innerHTML = '';
            if (filtersContainer) filtersContainer.innerHTML = '';
            renderXmlTemplateButtons(); // Обновляем кнопки после очистки таблицы
        });
    }

    // Рендерим сохраненные данные
    if (window.ExportSavedData) {
        const savedColumns = window.ExportSavedData.columns;
        const savedFilters = window.ExportSavedData.filters;

        if (savedColumns && Object.keys(savedColumns).length > 0) {
            Object.values(savedColumns).forEach(colData => addColumnRow(colData));
        } else {
            if (columnsContainer) addColumnRow();
        }

        if (savedFilters && Object.keys(savedFilters).length > 0) {
            Object.values(savedFilters).forEach(filterData => addFilterRow(filterData));
        }
    }

    // Инициализируем XML кнопки и обсервер после загрузки всех колонок
    renderXmlTemplateButtons();

    if (columnsContainer) {
        // Делегирование событий: реагируем на смену поля в select
        columnsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('target-select')) {
                renderXmlTemplateButtons();
            }
        });

        // Следим за добавлением и удалением элементов DOM
        const observer = new MutationObserver(() => renderXmlTemplateButtons());
        observer.observe(columnsContainer, { childList: true, subtree: true });
    }

});
