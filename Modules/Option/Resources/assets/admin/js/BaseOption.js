export default class {
    addOptionsErrors(errors) {
        for (let key in errors) {
            const inputField = this.getInputFieldForErrorKey(key);

            if (!inputField.length) {
                continue;
            }

            inputField.addClass('is-invalid');

            const option = inputField.closest('.accordion-item, .option');

            option.addClass('option-has-errors');

            const accordionButton = option.find('.accordion-button').first();

            accordionButton.addClass('has-error');

            if (!accordionButton.find('.option-error-icon').length) {
                accordionButton.prepend(
                    `<i class="bx bx-error fs-18 align-middle me-1 text-danger option-error-icon"></i>`
                );
            }

            const parent = inputField.closest('.form-group').length
                ? inputField.closest('.form-group')
                : inputField.parent();

            if (!parent.find(`[data-error-key="${key}"]`).length) {
                parent.append(
                    `<div class="invalid-feedback d-block" data-error-key="${key}">${errors[key][0]}</div>`
                );
            }
        }
    }


    getInputFieldForErrorKey(key) {
        const name = this.errorKeyToInputName(key);

        let field = $(`[name="${name}"]`);

        if (field.length) {
            return field;
        }

        const id = this.errorKeyToInputId(key);

        return $(`#${id}`);
    }

    errorKeyToInputName(key) {
        const parts = key.split('.');

        return parts.shift() + parts.map((part) => `[${part}]`).join('');
    }

    errorKeyToInputId(key) {
        return key
            .replaceAll('.', '-')
            .replaceAll('_', '-');
    }

    getRowTemplate(data) {
        let template = _.template($("#option-select-values-template").html());

        data.errors = Korf.data['errors'] || {};

        return $(template(data));
    }

    changeOptionType({ optionId, type, values = [] }) {
        let optionValuesElement = this.getOptionValuesElement(optionId);
        let templateType = this.getTemplateType(type, optionValuesElement);

        let optionValuesData = {
            optionId,
            value: { id: "", translations: [], price: "", price_type: "fixed" },
            errors: Korf.data['errors'] || {}
        };

        if (this.shouldNotChangeTemplate(templateType, optionValuesElement)) {
            return;
        }

        if (values.length !== 0 && templateType === "text") {
            optionValuesData.value = values[0];
        }

        let template = _.template($(`#option-${templateType}-template`).html());

        optionValuesElement.html(template(optionValuesData));

        if (templateType === "select") {
            this.addOptionRowEventListener(optionId);
            this.addOptionRows({ optionId, values });

            if (values.length === 0) {
                this.getAddNewRowButton(optionId).trigger("click");
            }
        }
    }

    addOptionRows({ optionId, values }) {
        const supportedLocales = Korf.data['supported_locales'] || [];

        for (let [index, value] of values.entries()) {
            // НОРМАЛИЗАЦИЯ: Если нет translations (пришло из old()), собираем их вручную
            if (!value.translations) {
                value.translations = [];
                supportedLocales.forEach(locale => {
                    // Проверяем наличие ключа локали (например, value.ru.label)
                    if (value[locale] && value[locale].label !== undefined) {
                        value.translations.push({
                            locale: locale,
                            label: value[locale].label
                        });
                    }
                });
            }

            this.addOptionRow({
                optionId,
                valueId: index,
                value,
            });
        }
    }

    getTemplateType(type) {
        if (this.templateTypeIsText(type)) {
            return "text";
        }

        if (this.templateTypeIsSelect(type)) {
            return "select";
        }
    }

    templateTypeIsText(type) {
        return ["field", "textarea", "date", "date_time", "time"].includes(
            type
        );
    }

    templateTypeIsSelect(type) {
        return [
            "dropdown",
            "checkbox",
            "checkbox_custom",
            "radio",
            "radio_custom",
            "multiple_select",
        ].includes(type);
    }

    shouldNotChangeTemplate(templateType, optionValuesElement) {
        return (
            templateType === undefined ||
            this.alreadyHasCurrentTemplate(templateType, optionValuesElement)
        );
    }

    alreadyHasCurrentTemplate(templateType, optionValuesElement) {
        if (templateType === "text") {
            return optionValuesElement.children().hasClass("option-text");
        }

        if (templateType === "select") {
            return optionValuesElement.children().hasClass("option-select");
        }
    }

    initOptionRow(template, selectValues) {
        if (selectValues.length !== 0 && !selectValues.is(".sortable")) {
            this.makeSortable(selectValues[0]);

            selectValues.addClass("sortable");
        }

        this.deleteOptionRowEventListener(template);
    }

    deleteOptionRowEventListener(row) {
        row.find(".delete-row").on("click", (e) => {
            $(e.currentTarget).closest(".option-row").remove();
        });
    }

    makeSortable(el) {
        Sortable.create(el, {
            handle: ".drag-handle",
            animation: 150,
        });
    }
}
