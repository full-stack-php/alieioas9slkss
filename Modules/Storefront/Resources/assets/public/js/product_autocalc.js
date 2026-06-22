export default class {
    constructor() {
        const {
            base_price,
            base_special_price,
            animate_delay,
            currency
        } = Korf.data.autocalc;

        this.basePrice = parseFloat(base_price);
        this.baseSpecialPrice = parseFloat(base_special_price || 0);

        this.animateDelay = animate_delay;
        this.currencyConfig = currency;

        this.finalPrice = this.basePrice;
        this.currentPrice = this.basePrice;

        this.finalSpecialPrice = this.baseSpecialPrice;
        this.currentSpecialPrice = this.baseSpecialPrice;

        this.timeoutId = 0;

        this.selectors = {
            container: '#main_product',
            priceDisplay: '.price_value, .autocalc-product-price:not(.special_value)',
            specialDisplay: '.special_value',
            oldPriceWrapper: '.price-old',
            newPriceWrapper: '.price-new',
            quantityInput: 'input[name="quantity"]',
            options: 'input:checked, option:selected, input[name="packaging_id"]:checked',
            triggerElements: 'input[type="checkbox"], input[type="radio"], select, input[name="packaging_id"]'
        };

        this.init();
    }

    init() {
        this.bindEvents();
        this.startQuantityWatcher();
        this.recalculate();
    }

    bindEvents() {
        $(document).on('change', this.selectors.triggerElements, () => {
            this.recalculate();
        });
    }

    startQuantityWatcher() {
        const $qty = $(this.selectors.quantityInput);
        let lastVal = $qty.val();
        setInterval(() => {
            const currentVal = $qty.val();
            if (currentVal !== lastVal) {
                lastVal = currentVal;
                this.recalculate();
            }
        }, 250);
    }

    recalculate() {
        const quantity = Number($(this.selectors.quantityInput).val()) || 1;

        let total = this.basePrice;
        let totalSpecial = this.baseSpecialPrice;

        const hasDiscount = (this.baseSpecialPrice > 0);

        $(this.selectors.options).each((_, el) => {
            const $el = $(el);
            if ($el.is(':disabled') || $el.closest('select').is(':disabled')) {
                return;
            }

            const prefix = $el.data('prefix');
            const price = parseFloat($el.data('price') || 0);

            const specPrice = $el[0].hasAttribute('data-price-special')
                ? parseFloat($el.data('price-special') || 0)
                : price;

            if (prefix === 'fixed' && price > 0) {
                total = price;
                if (hasDiscount) totalSpecial = specPrice;
            }
            else if (prefix === 'percent' && price > 0) {
                total += this.basePrice * (price / 100);
                if (hasDiscount) totalSpecial += this.baseSpecialPrice * (specPrice / 100);
            }
            else if (prefix === '+') {
                total += price;
                if (hasDiscount) totalSpecial += specPrice;
            }
            else if (prefix === '-') {
                total -= price;
                if (hasDiscount) totalSpecial -= specPrice;
            }
        });

        total *= quantity;

        if (hasDiscount) {
            totalSpecial *= quantity;
            if (totalSpecial < 0) totalSpecial = 0;
        }
        if (total < 0) total = 0;

        this.animatePrices(total, totalSpecial);
    }

    animatePrices(newPrice, newSpecial) {
        this.finalPrice = newPrice;
        this.finalSpecialPrice = newSpecial;

        clearTimeout(this.timeoutId);
        this.runAnimationStep();
    }

    runAnimationStep() {
        this.currentPrice = this.lerp(this.currentPrice, this.finalPrice, 0.2);
        this.currentSpecialPrice = this.lerp(this.currentSpecialPrice, this.finalSpecialPrice, 0.2);

        this.updateHtml(this.currentPrice, this.currentSpecialPrice);

        if (Math.abs(this.currentPrice - this.finalPrice) > 0.1 ||
            (this.finalSpecialPrice > 0 && Math.abs(this.currentSpecialPrice - this.finalSpecialPrice) > 0.1)) {
            this.timeoutId = setTimeout(() => this.runAnimationStep(), this.animateDelay);
        } else {
            this.renderFinal();
        }
    }

    renderFinal() {
        this.updateHtml(this.finalPrice, this.finalSpecialPrice);
    }

    updateHtml(renderPrice, renderSpecial) {
        const $containers = $('.price-update-container');

        if (this.finalSpecialPrice > 0 && this.finalSpecialPrice !== this.finalPrice) {
            $containers.html(`
                <span class="price-old"><span class="price_value">${this.formatPrice(renderPrice)}</span></span>
                <span class="price-new"><span class="special_value">${this.formatPrice(renderSpecial)}</span></span>
            `);
        } else {
            $containers.html(`<span class="autocalc-product-price">${this.formatPrice(renderPrice)}</span>`);
        }
    }

    lerp(start, end, amt) {
        return (1 - amt) * start + amt * end;
    }

    formatPrice(n) {
        const { suffix, thousands_separator } = this.currencyConfig;
        const number = Math.abs(n).toFixed(0);
        const i = parseInt(number, 10).toString();
        const j = i.length > 3 ? i.length % 3 : 0;

        return (j ? i.substr(0, j) + thousands_separator : '') +
            i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_separator) + suffix;
    }
}
