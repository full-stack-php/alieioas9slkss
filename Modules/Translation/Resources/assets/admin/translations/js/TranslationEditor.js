export default class {
    constructor() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        this.init();
    }

    async init() {
        if ($.fn.popover) {
            $.fn.popover.Constructor = $.fn.popover.Constructor || {};
            $.fn.popover.Constructor.DEFAULTS = bootstrap.Popover.Default;
        }
        if ($.fn.tooltip) {
            $.fn.tooltip.Constructor = $.fn.tooltip.Constructor || {};
            $.fn.tooltip.Constructor.DEFAULTS = bootstrap.Tooltip.Default;
        }
        await import("x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js");
        await import("x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css");
        this.setupEditable();
    }

    setupEditable() {
        $.fn.editable.defaults.mode = 'inline';

        $.fn.editableform.buttons =
            '<button type="submit" class="btn btn-primary btn-md editable-submit"><i class=\'bx  bx-check\'></i></button>' +
            '<button type="button" class="btn btn-secondary btn-md editable-cancel"><i class=\'bx  bx-x\'></i></button>';

        const self = this;
        $('.translation').editable({
            url: function(params) {
                return self.update.call(this, params);
            },
            type: 'text',
            mode: 'inline',
            send: 'always',
        });
    }

    update(params) {
        const $el = this;
        console.log(params);
        console.log(this.dataset);

        return $.ajax({
            url: `${Korf.baseUrl}/admin/languages/${$el.dataset.locale}/translations/${$el.dataset.key}`,
            type: 'PUT',
            data: {
                locale: $el.dataset.locale,
                value: params.value,
            },
            success: (res) => window.success && window.success(res.message),
            error: (xhr) => window.error && window.error(xhr.responseJSON?.message)
        });
    }
}
