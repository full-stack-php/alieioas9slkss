import MediaPicker from './MediaPicker';

export default class {
    constructor() {
        $('.file-picker').on('click', (e) => {
            this.pickFile(e);
        });

        this.sortable();
        this.removeFileEventListener();
    }

    pickFile(e) {
        let inputName = e.currentTarget.dataset.inputName;
        let multiple = e.currentTarget.hasAttribute('data-multiple');

        let picker = new MediaPicker({
            type: null,
            multiple,
        });

        picker.on('select', (file) => {
            this.addFile(inputName, file, multiple, e.currentTarget);
        });
    }

    addFile(inputName, file, multiple, target) {
        let html = this.getTemplate(inputName, file);

        if (multiple) {
            let wrapper = $(target).next('.multiple-files');

            wrapper.find('.image-holder.placeholder').remove();
            wrapper.find('.file-list').append(html);

            return;
        }

        $(target).siblings('.single-file').html(html);
    }

    getTemplate(inputName, file) {
        const isImage = file.type === 'image';

        const preview = isImage
            ? `<img src="${file.path}" alt="${file.filename || ''}">`
            : `<i class="file-icon fa ${file.icon || 'fa-file-o'}"></i>`;

        return $(`
            <div class="image-holder file-holder">
                ${preview}

                <div class="file-name small text-center mt-1">
                    ${file.filename || ''}
                </div>

                <button type="button" class="btn remove-file">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M6.00098 17.9995L17.9999 6.00053" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.9999 17.9995L6.00098 6.00055" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <input type="hidden" name="${inputName}" value="${file.id}">
            </div>
        `);
    }

    sortable() {
        $('.file-list').each(function () {
            Sortable.create(this, { animation: 150 });
        });
    }

    removeFileEventListener() {
        $('.image-holder-wrapper').on('click', '.remove-file', (e) => {
            e.preventDefault();

            let wrapper = $(e.currentTarget).closest('.image-holder-wrapper');

            $(e.currentTarget).parent().remove();

            if (wrapper.find('.image-holder').length === 0) {
                wrapper.html(this.getPlaceholder());
            }
        });
    }

    getPlaceholder() {
        return `
            <div class="image-holder placeholder cursor-auto">
                <i class="h1 fa fa-file-o"></i>
            </div>
        `;
    }
}
