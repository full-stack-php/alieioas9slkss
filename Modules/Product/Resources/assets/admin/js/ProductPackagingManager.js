export default class {
    constructor() {
        this.packContainer = document.getElementById('product-packagings');
        this.packTemplate = document.getElementById('packaging-template')?.innerHTML;
        this.addPackBtn = document.getElementById('add-new-packaging');

        this.packIndex = this.packContainer?.querySelectorAll('tr.packaging-row').length || 0;

        if (this.packContainer) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.addPackBtn?.addEventListener('click', (e) => {
            e.preventDefault();

            this.renderRow(this.packContainer, this.packTemplate, this.packIndex++);
        });

        document.addEventListener('click', (e) => {
            const deletePack = e.target.closest('.delete-row');

            if (deletePack) {
                e.preventDefault();
                deletePack.closest('tr.packaging-row')?.remove();
            }
        });
    }

    renderRow(container, template, index) {
        if (!container || !template) return null;

        const html = template.replace(/__INDEX__/g, index);

        const tempTable = document.createElement('table');
        tempTable.innerHTML = `<tbody>${html.trim()}</tbody>`;

        const row = tempTable.querySelector('tbody').firstElementChild;
        container.appendChild(row);

        return row;
    }
}
