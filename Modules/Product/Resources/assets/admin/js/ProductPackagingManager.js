export default class {
    constructor() {
        this.packContainer = document.getElementById('product-packagings');
        this.giftContainer = document.getElementById('product-gift-packagings');

        this.packTemplate = document.getElementById('packaging-template')?.innerHTML;
        this.giftTemplate = document.getElementById('gift-packaging-template')?.innerHTML;

        this.addPackBtn = document.getElementById('add-new-packaging');
        this.addGiftBtn = document.getElementById('add-new-packaging-gift');

        this.packIndex = this.packContainer?.querySelectorAll('tr.packaging-row').length || 0;
        this.giftIndex = this.giftContainer?.querySelectorAll('tr.gift-row').length || 0;

        if (this.packContainer && this.giftContainer) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.updateGiftSelectors();
    }

    bindEvents() {
        this.addPackBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            this.renderRow(this.packContainer, this.packTemplate, this.packIndex++);
            this.updateGiftSelectors();
        });

        this.addGiftBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            this.renderRow(this.giftContainer, this.giftTemplate, this.giftIndex++);
            this.updateGiftSelectors();
        });

        document.addEventListener('click', (e) => {
            const delPack = e.target.closest('.delete-row');
            const delGift = e.target.closest('.delete-gift-row');

            if (delPack) {
                e.preventDefault();
                delPack.closest('tr').remove();
            }

            if (delGift) {
                e.preventDefault();
                delGift.closest('tr').remove();
                this.updateGiftSelectors();
            }
        });

        this.giftContainer.addEventListener('input', (e) => {
            if (e.target.matches('.gift-name-input') || e.target.matches('.gift-qty-input') || e.target.name.includes('[name]')) {
                this.updateGiftSelectors();
            }
        });
    }

    renderRow(container, template, index) {
        if (!template) return;
        let html = template.replace(/__INDEX__/g, index);
        const tempTable = document.createElement('table');
        tempTable.innerHTML = `<tbody>${html.trim()}</tbody>`;
        const row = tempTable.querySelector('tbody').firstElementChild;
        container.appendChild(row);
        return row;
    }

    updateGiftSelectors() {
        const availableGifts = [];
        const currentLocale = Korf.data['current_locale'] || 'en';

        this.giftContainer.querySelectorAll('tr.gift-row').forEach(row => {
            const idInput = row.querySelector('input[name*="[id]"]');
            const realId = idInput ? idInput.value : null;

            const nameInput = row.querySelector(`input[name*="[${currentLocale}][name]"]`) || row.querySelector('.gift-name-input');
            const qtyInput = row.querySelector('.gift-qty-input');

            if (qtyInput) {
                const match = qtyInput.name.match(/\[(\d+)\]/);
                const tempIdx = match ? match[1] : null;

                const nameVal = nameInput?.value.trim() || '';
                const qtyVal = qtyInput.value || '0';

                if (tempIdx !== null) {
                    const giftValue = (realId && realId !== '') ? realId : `idx_${tempIdx}`;

                    availableGifts.push({
                        id: giftValue,
                        label: nameVal ? `${nameVal} (${qtyVal} шт.)` : `Подарок #${parseInt(tempIdx) + 1} (${qtyVal} шт.)`
                    });
                }
            }
        });

        const packSelectors = this.packContainer.querySelectorAll('.gift-id-selector');

        packSelectors.forEach(select => {
            const savedId = select.getAttribute('data-selected');
            const currentVal = select.value || savedId;

            const noGiftText = select.options[0]?.text || '---';
            select.innerHTML = `<option value="">${noGiftText}</option>`;

            availableGifts.forEach(gift => {
                const option = new Option(gift.label, gift.id);
                if (currentVal !== null && String(gift.id) === String(currentVal)) {
                    option.selected = true;
                }
                select.add(option);
            });

            if (select.value) {
                select.setAttribute('data-selected', select.value);
            } else {
                select.setAttribute('data-selected', '');
            }
        });
    }
}
