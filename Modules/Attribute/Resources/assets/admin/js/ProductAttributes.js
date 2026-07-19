import Choices from 'choices.js';

export default class {
    constructor() {
        this.attributeCount = 0;
        this.choicesInstances = {};

        this.addProductAttributes(Korf.data['product.attributes']);

        if (this.attributeCount === 0) {
           // this.addProductAttribute();
        }

        this.addProductAttributesErrors(Korf.errors['product.attributes']);

        this.eventListeners();
        this.sortable();
    }

    addProductAttributes(attributes) {
        for (let attribute of attributes) {
            this.addProductAttribute(attribute);
        }
    }

    addProductAttribute(attribute = {}) {
        let template = _.template($('#product-attribute-template').html());
        let idCount = this.attributeCount++;
        let html = template({ attributeId: idCount, attribute });

        let $html = $(html);
        $('#product-attributes').append($html);

        this.initChoicesInRow(
            $html,
            idCount,
            attribute
        );

        this.updatePositions();
    }

    initChoicesInRow($row, attributeId, attributeData = {}) {
        const valuesEl = $row.find(`select[id="attributes.${attributeId}.values"]`)[0];

        if (valuesEl) {
            // Если вдруг экземпляр уже существует, уничтожаем его
            if (this.choicesInstances[valuesEl.id]) {
                this.choicesInstances[valuesEl.id].destroy();
            }

            const instance = new Choices(valuesEl, {
                removeItemButton: true,
                placeholderValue: trans('admin::admin.table.select_option'),
                searchPlaceholderValue: trans('admin::admin.table.search_here'),
                itemSelectText: '',
                shouldSort: false,
                allowHTML: true
            });

            this.choicesInstances[valuesEl.id] = instance;
        }

        const attributeSelect = $row.find('.attribute')[0];
        if (attributeSelect && attributeSelect.value) {
            // Передаем ID значений из пришедших данных (attributeData), чтобы пометить их выбранными
            const selectedIds = _.map(attributeData.values, (v) => String(v.attribute_value_id || v.id));

            // Вызываем один раз для начальной отрисовки
            this.changeProductAttributeValues(attributeSelect, false, selectedIds);
        }
    }

    changeProductAttributeValues(attributeEl, clearSelected = true, forceSelectedIds = []) {
        const attributeId = attributeEl.dataset.attributeId;
        const valuesElementId = `attributes.${attributeId}.values`;
        const choicesInstance = this.choicesInstances[valuesElementId];
        if (!choicesInstance) return;

        const currentValue = attributeEl.value;
        const $selectedOption = $(attributeEl).find(`option[value="${currentValue}"]`);
        const rawData = $selectedOption.attr('data-values');

        let valuesData = {};
        try {
            valuesData = JSON.parse(rawData || '{}');
        } catch (e) {
            console.error("Error parsing JSON", e);
        }

        const currentSelectedValues = forceSelectedIds.length > 0
            ? forceSelectedIds
            : choicesInstance.getValue(true);

        const newChoices = Object.keys(valuesData).map(id => {
            return {
                value: String(id),
                label: valuesData[id],
                selected: currentSelectedValues.includes(String(id)),
            };
        });

        choicesInstance.setChoices(newChoices, 'value', 'label', true);
    }

    addProductAttributesErrors(errors) {
        for (let key in errors) {
            const inputField = this.getInputFieldForErrorKey(key);

            if (!inputField.length) {
                continue;
            }

            inputField.addClass('is-invalid');

            this.markChoicesAsInvalid(inputField);

            const row = inputField.closest('tr');

            row.addClass('attribute-has-errors');

            const parent = inputField.closest('.form-group').length
                ? inputField.closest('.form-group')
                : inputField.closest('td');

            if (!parent.find(`[data-error-key="${key}"]`).length) {
                parent.append(
                    `<div class="invalid-feedback d-block" data-error-key="${key}">${errors[key][0]}</div>`
                );
            }
        }
    }

    getInputFieldForErrorKey(key) {
        const names = this.errorKeyToInputNames(key);

        for (let name of names) {
            const field = $(`[name="${name}"]`);

            if (field.length) {
                return field;
            }
        }

        const id = $.escapeSelector(key);

        return $(`#${id}`);
    }

    errorKeyToInputNames(key) {
        const parts = key.split('.');
        const first = parts.shift();

        const name = first + parts.map((part) => `[${part}]`).join('');

        return [
            name,
            `${name}[]`,
        ];
    }

    markChoicesAsInvalid(inputField) {
        const choices = inputField.closest('.form-group').find('.choices');

        if (choices.length) {
            choices.addClass('is-invalid');
            choices.find('.choices__inner').addClass('is-invalid');
        }
    }

    deleteProductAttribute(e) {
        const $tr = $(e.currentTarget).closest('tr');

        $tr.find('select').each((i, el) => {
            if (this.choicesInstances[el.id]) {
                this.choicesInstances[el.id].destroy();

                delete this.choicesInstances[el.id];
            }
        });

        $tr.remove();

        this.updatePositions();
    }

    eventListeners() {
        $('#add-new-attribute').on('click', () => this.addProductAttribute());
        $('#product-attributes').on('click', '.delete-row', (e) => this.deleteProductAttribute(e));

        // Обычный селект отлично работает с нативным событием change
        $('#product-attributes-wrapper').on('change', '.attribute', (e) => {
            this.changeProductAttributeValues(e.currentTarget, true);
        });
    }


    sortable() {
        const element = document.getElementById(
            'product-attributes'
        );

        if (!element) {
            return;
        }

        Sortable.create(element, {
            handle: '.drag-icon',
            animation: 150,

            onEnd: () => {
                this.updatePositions();
            },
        });
    }

    updatePositions() {
        $('#product-attributes > tr').each(
            function (position) {
                $(this)
                    .find('.attribute-position')
                    .val(position);
            }
        );
    }
}
