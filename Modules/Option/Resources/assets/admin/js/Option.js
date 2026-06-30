import BaseOption from './BaseOption';

export default class extends BaseOption {
    constructor() {
        super();

        let values = Korf.data['option.values'];

        $('#type').on('change', (e) => {
            super.changeOptionType({ type: e.currentTarget.value, values });
            super.addOptionsErrors(Korf.errors['option.values']);
        });

        $('#type').trigger('change');

        window.admin.removeSubmitButtonOffsetOn('#values');
    }

    addOptionRow({ valueId, value = { label: '' } }) {
        let template = this.getRowTemplate({ optionId: undefined, valueId, value });

        let selectValues = $('#select-values').append(template);

        super.initOptionRow(template, selectValues);
    }

    addOptionRowEventListener() {
        $('#add-new-row').on('click', () => {
            let valueId = $('#option-values .option-row').length;

            this.addOptionRow({ valueId });
        });
    }

    getOptionValuesElement() {
        return $('#option-values');
    }

    getAddNewRowButton() {
        return $('#add-new-row');
    }

    getInputFieldForErrorKey(key) {
        return super.getInputFieldForErrorKey(key);
    }
}
