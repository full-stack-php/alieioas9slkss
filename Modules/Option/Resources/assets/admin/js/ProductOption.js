import BaseOption from './BaseOption';

export default class extends BaseOption {
    constructor() {
        super();
        this.optionsCount = 0;
        this.availableValues = {};

        const rawOptions = Korf.data['product.options'] || [];

        this.restoreOptions(rawOptions).then(() => {
            if (this.optionsCount > 3) {
                this.collapseOptions();
            }

            super.addOptionsErrors(Korf.errors['product.options'] || []);
        });

        $('#add-global-option').on('click', () => this.addGlobalOption());
    }

    async restoreOptions(options) {
        for (let option of options) {
            let globalId = option.option_id || option.id;

            if (!option.name && globalId && !isNaN(globalId)) {
                try {
                    await this.recoveryOptionFromApi(option, globalId);
                } catch (e) {
                    console.error("Не удалось загрузить опцию:", globalId);
                    this.addOption(option);
                }
            } else {
                this.addOption(option);
            }
        }
    }

    recoveryOptionFromApi(oldOption, globalId) {
        let url = Korf.data['get_options_link'].replace('REPLACE_ID', globalId);

        return new Promise((resolve) => {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: response => {
                    let fullData = response.data || response;
                    let mergedOption = {
                        ...fullData,
                        option_id: oldOption.option_id || fullData.id || oldOption.id,
                        product_option_id: oldOption.id || null,
                        is_required: oldOption.is_required,
                        values_assigned: oldOption.values || []
                    };
                    this.addOption(mergedOption);
                    resolve();
                },
                error: () => {
                    this.addOption(oldOption);
                    resolve();
                }
            });
        });
    }

    addOptions(options) {
        for (let option of options) {
            this.addOption(option);
        }
    }

    addGlobalOption() {
        let globalOptionId = $('#global-option').val();
        if (!globalOptionId) return;

        let url = Korf.data['get_options_link'].replace('REPLACE_ID', globalOptionId);

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: response => {
                this.addOption(response.data || response);
            },
        });
    }

    addOption(option) {
        let optionId = this.optionsCount++;

        option.option_id = option.option_id || option.id;
        option.is_required = [true, 1, "1"].includes(option.is_required);

        this.availableValues[optionId] = (option.option && option.option.values)
            ? option.option.values
            : (option.values || []);

        let template = _.template($('#option-template').html());
        let $html = $(template({ option, optionId }));

        $('#options-group').append($html);

        if (!$('#options-group').is('.sortable')) {
            super.makeSortable($('#options-group')[0]);
            $('#options-group').addClass('sortable');
        }

        this.deleteOptionEventListener($html);

        let assigned = option.values_assigned || (option.option ? option.values : []);

        this.addProductOptionType({
            optionId,
            type: option.type,
            assignedValues: assigned || [],
            allAvailable: this.availableValues[optionId]
        });
    }

    addProductOptionType({ optionId, type, assignedValues = [], allAvailable = [] }) {
        let optionValuesElement = this.getOptionValuesElement(optionId);
        let templateType = this.getTemplateType(type, optionValuesElement);

        if (this.shouldNotChangeTemplate(templateType, optionValuesElement)) {
            return;
        }

        let template = _.template($(`#option-${templateType}-template`).html());
        optionValuesElement.html(template({ optionId }));

        if (templateType === "select") {
            this.addOptionRowEventListener(optionId, allAvailable);
            this.deleteRowEventListener(optionId);

            assignedValues.forEach((value, index) => {
                this.addOptionRow({
                    optionId,
                    valueId: index,
                    value: value,
                    allAvailable: allAvailable
                });
            });

            if (assignedValues.length === 0) {
                this.addOptionRow({ optionId, valueId: 0, allAvailable });
            }
            let sortableElement = $(`#option-${optionId}-select-values`)[0];
            if (sortableElement) {
                this.makeSortable(sortableElement);
            }
        }
    }

    deleteRowEventListener(optionId) {
        this.getOptionValuesElement(optionId).on('click', '.delete-row', (e) => {
            e.preventDefault();
            $(e.currentTarget).closest('.option-row').remove();
            $('.tooltip').remove();
        });
    }

    addOptionRow({ optionId, valueId, value = {}, allAvailable = [] }) {
        // В ProductOptionValue (БД) это option_value_id. В old() тоже.
        let currentVId = value.option_value_id || value.id;

        let originalValue = allAvailable.find(v => String(v.id) === String(currentVId)) || {};

        let templateData = {
            optionId,
            valueId,
            value: {
                option_value_id: currentVId,
                label: originalValue.label || value.label || '',
                // Универсальный парсинг цены
                price: (value.price && typeof value.price === 'object') ? value.price.amount : (value.price || 0),
                price_type: value.price_type || 'fixed',
                special_price: (value.special_price && typeof value.special_price === 'object') ? value.special_price.amount : (value.special_price || 0),
                special_price_type: value.special_price_type || 'fixed'
            },
            allAvailable: allAvailable
        };

        let template = _.template($('#option-select-values-template').html());
        $(`#option-${optionId}-select-values`).append(template(templateData));
    }

    addOptionRowEventListener(optionId, allAvailable) {
        $(`#option-${optionId}-add-new-row`).off('click').on('click', (e) => {
            e.preventDefault();
            let valueId = $(`#option-${optionId}-select-values .option-row`).length;
            this.addOptionRow({ optionId, valueId, allAvailable });
        });
    }

    getOptionValuesElement(optionId) {
        return $(`#option-${optionId}-values`);
    }

    deleteOptionEventListener(option) {
        option.find('.delete-option').on('click', (e) => {
            $(e.currentTarget).closest('.accordion-item').remove();
        });
    }

    makeSortable(el) {
        Sortable.create(el, {
            handle: ".drag-handle",
            animation: 150,
        });
    }
}
