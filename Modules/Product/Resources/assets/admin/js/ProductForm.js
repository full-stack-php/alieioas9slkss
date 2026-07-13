export default class {
    constructor() {
        this.managerStock();

        $('#product-create-form, #product-edit-form').on('submit', this.submit);
    }

    managerStock() {
        $('#stock_status').on('change', (e) => {
            const status = Number(e.currentTarget.value);

            if (status === 1) {
                $('#qty-field').removeClass('hide');
            } else {
                $('#qty-field').addClass('hide');
            }

            if ([0, 1].includes(status)) {
                $('#in-stock-field').removeClass('hide');
            } else {
                $('#in-stock-field').addClass('hide');
            }
        });
    }

    submit(e) {
        e.preventDefault();
        DataTable.removeLengthFields();
        window.form.appendHiddenInputs('#product-create-form, #product-edit-form', 'colors', DataTable.getSelectedIds('#colors .table'));
        window.form.appendHiddenInputs('#product-create-form, #product-edit-form', 'cross_sells', DataTable.getSelectedIds('#cross_sells .table'));
        window.form.appendHiddenInputs('#product-create-form, #product-edit-form', 'related_products', DataTable.getSelectedIds('#related_products .table'));
        e.currentTarget.submit();
    }
}
