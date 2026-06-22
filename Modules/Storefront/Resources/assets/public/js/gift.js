export default class {
    constructor() {
        this.selectors = {
            quantityInput: 'input[name="quantity"]',
            giftItem: '.product-gifts__item',
            giftCheckbox: '.gift-checkbox',
            container: '.product-gifts'
        };

        if (document.querySelector(this.selectors.container)) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.startWatcher();
        this.checkAvailability($(this.selectors.quantityInput).val());
    }

    bindEvents() {
        const _this = this;
        $(document).on('change', this.selectors.giftCheckbox, function() {
            if ($(this).is(':checked')) {
                $(_this.selectors.giftCheckbox).not(this).prop('checked', false);
                $(_this.selectors.giftItem).removeClass('is-selected');
                $(this).closest(_this.selectors.giftItem).addClass('is-selected');
            } else {
                $(this).closest(_this.selectors.giftItem).removeClass('is-selected');
            }
        });
        $(document).on('input change', this.selectors.quantityInput, (e) => {
            this.checkAvailability($(e.target).val());
        });
    }

    startWatcher() {
        const $qty = $(this.selectors.quantityInput);
        if (!$qty.length) return;

        let lastVal = $qty.val();
        setInterval(() => {
            const currentVal = $qty.val();
            if (currentVal !== lastVal) {
                lastVal = currentVal;
                this.checkAvailability(currentVal);
            }
        }, 200);
    }

    checkAvailability(quantity) {
        const currentQty = parseInt(quantity) || 0;
        const _this = this;

        $(this.selectors.giftItem).each(function() {
            const $item = $(this);
            const minQty = parseInt($item.data('min-qty') || 1);
            const $checkbox = $item.find(_this.selectors.giftCheckbox);

            if (currentQty >= minQty) {
                _this.enableItem($item, $checkbox);
            } else {
                _this.disableItem($item, $checkbox);
            }
        });
    }

    enableItem($item, $checkbox) {
        $item.removeClass('is-disabled').css({
            'opacity': '1',
            'pointer-events': 'auto',
            'filter': 'none'
        });
        $checkbox.prop('disabled', false);
    }

    disableItem($item, $checkbox) {
        $item.addClass('is-disabled').css({
            'opacity': '0.4',
            'pointer-events': 'none',
            'filter': 'grayscale(1)'
        });

        if ($checkbox.is(':checked')) {
            $checkbox.prop('checked', false).trigger('change');
            $item.removeClass('is-selected');
        }
        $checkbox.prop('disabled', true);
    }
}
