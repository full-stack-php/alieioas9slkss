import CategoryTree from "./CategoryTree";

export default class {
    constructor() {
        let tree = $(".category-tree");

        new CategoryTree(this, tree);

        this.collapseAll(tree);
        this.expandAll(tree);
        this.addRootCategory();
        this.addSubCategory();
        this.removeSubmitButtonOffsetOn(
            "#image",
            ".category-details-tab li > a"
        );

        $("#category-form").on("submit", this.submit);
    }

    collapseAll(tree) {
        $(".collapse-all").on("click", (e) => {
            e.preventDefault();

            tree.jstree("close_all");
        });
    }

    expandAll(tree) {
        $(".expand-all").on("click", (e) => {
            e.preventDefault();

            tree.jstree("open_all");
        });
    }

    addRootCategory() {
        $(".add-root-category").on("click", () => {
            this.loading(true);

            $(".add-sub-category").addClass("disabled");

            $(".category-tree").jstree("deselect_all");

            this.clear();

            // Intentionally delay 150ms so that user feel new form is loaded.
            setTimeout(this.loading, 150, false);
        });
    }

    addSubCategory() {
        $(".add-sub-category").on("click", () => {
            let selectedId = $(".category-tree").jstree("get_selected")[0];

            if (selectedId === undefined) {
                return;
            }

            this.clear();
            $('.faq-item').parent().not('#faq-template *').remove();
            this.loading(true);

            window.form.appendHiddenInput(
                "#category-form",
                "parent_id",
                selectedId
            );

            // Intentionally delay 150ms so that user feel new form is loaded.
            setTimeout(this.loading, 150, false);
        });
    }

    fetchCategory(id) {
        const self = this;

        this.loading(true);
        $(".add-sub-category").removeClass("disabled");

        $.ajax({
            url: `${Korf.baseUrl}/admin/categories/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                self.update(data);
                self.loading(false);
            },
            error: function(xhr) {
                let message = "Произошла ошибка";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                if (typeof self.error === 'function') {
                    self.error(message);
                }

                self.loading(false);
            }
        });
    }

    renderFaqItem(container, template, containsCategories) {
        let newFaqHtml = template.replace(/__FAQ_INDEX__/g, window.faqIndex);

        if (containsCategories) {
            newFaqHtml = newFaqHtml.replace(/col-md-6 col-lg-6/g, 'col-md-12 col-lg-12');
            newFaqHtml = newFaqHtml.replace(/rows="10"/g, 'rows="3"');
        }

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newFaqHtml.trim();

        // ИСПОЛЬЗУЕМ firstElementChild, чтобы пропустить пробелы и текстовые узлы
        const newNode = tempDiv.firstElementChild;

        if (!newNode) {
            console.error("Ошибка: Не удалось создать Node из шаблона. Проверьте содержимое #faq-template.");
            return null;
        }

        container.appendChild(newNode);

        const newTabs = newNode.querySelector(`.nav-link.active`);
        if (newTabs) {
            newTabs.click();
        }

        window.faqIndex++;

        return newNode;
    }

    update(category) {

        window.form.removeErrors();
        $(".btn-delete").removeClass("d-none");
        $(".form-group .help-block").remove();

        $("#confirmation-form").attr(
            "action",
            `${window.Korf.baseUrl}/admin/categories/${category.id}`
        );

        $("#id-field").removeClass("d-none");
        $("#id").val(category.id);

        const container = document.getElementById('faq-container'); // убедитесь, что ID верный
        const template = document.getElementById('faq-template').innerHTML; // ваш шаблон
        const containsCategories = window.location.href.includes('categories');

        $('.faq-item').parent().not('#faq-template *').remove();
        window.faqIndex = 0;

        if (category.data_faq && category.data_faq.length > 0) {
            category.data_faq.forEach((faq) => {
                const faqNode = this.renderFaqItem(container, template, containsCategories);

                faq.translations.forEach((translation) => {
                    const locale = translation.locale;
                    const indexInName = window.faqIndex - 1;

                    $(faqNode).find(`[name*="[${locale}][question]"]`).val(translation.question);
                    $(faqNode).find(`[name*="[${locale}][answer]"]`).val(translation.answer);
                });
            });
        }


        category.translations_data.map((item) => {
            let selectorName = `#${item.locale}\\[name\\]`,
                selectorH1Name = `#${item.locale}\\[h1_name\\]`,
                selectorDescription = `#${item.locale}\\[description\\]`;

            $(selectorName).val(item.name);
            $(selectorH1Name).val(item.h1_name);
            $(selectorDescription).val(item.description);

            let editor = tinymce.get($(selectorDescription).attr('id'));

            if (editor) {
                editor.setContent(item.description??'');
            } else {
                tinyMCE();
            }
        })


        category.meta_data?.map((item) => {
            let selectorName = `#meta\\[${item.locale}\\]\\[meta_title\\]`,
                selectorDescription = `#meta\\[${item.locale}\\]\\[meta_description\\]`;

            $(selectorName).val(item.meta_title);
            $(selectorDescription).val(item.meta_description);
        })

        $("#slug").val(category.slug);
        $("#slug-field").removeClass("d-none");
        $(".category-details-tab .seo-tab").removeClass("d-none");

        $("#is_searchable").prop("checked", category.is_searchable);
        $("#is_active").prop("checked", category.is_active);

        $(".banner .image-holder-wrapper").html(
            this.categoryImage("banner", category.banner)
        );
        $(".logo .image-holder-wrapper").html(
            this.categoryImage("logo", category.logo)
        );

        $('#category-form input[name="parent_id"]').remove();
    }

    categoryImage(fieldName, file) {
        if (!file || !file.exists) {
            return this.imagePlaceholder();
        }

        return `
            <div class="image-holder">
                <img src="${file.path}">
                <button type="button" class="btn remove-image" data-input-name="files[${fieldName}]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M6.00098 17.9995L17.9999 6.00053" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.9999 17.9995L6.00098 6.00055" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <input type="hidden" name="files[${fieldName}]" value="${file.id}">
            </div>
        `;
    }

    clear() {
        $("#id-field").addClass("d-none");

        $("#id").val("");
        $("#category-form input[type=text], #category-form textarea").val("");

        $("#slug").val("");
        $("#slug-field").addClass("d-none");
        $(".category-details-tab .seo-tab").addClass("d-none");

        $("#is_searchable").prop("checked", false);
        $("#is_active").prop("checked", false);

        $(".logo .image-holder-wrapper").html(this.imagePlaceholder());
        $(".banner .image-holder-wrapper").html(this.imagePlaceholder());

        $(".btn-delete").addClass("d-none");
        $(".form-group .help-block").remove();

        $('#category-form input[name="parent_id"]').remove();

        $(".general-information-tab a").click();
    }

    imagePlaceholder() {
        return `
            <div class="image-holder placeholder">
                <i class="h1 bx bx-cloud-upload"></i>
            </div>
        `;
    }

    loading(state) {
        if (state === true) {
            $(".overlay.loader").removeClass("d-none");
        } else {
            $(".overlay.loader").addClass("d-none");
        }
    }

    submit(e) {
        let selectedId = $(".category-tree").jstree("get_selected")[0];

        console.log(selectedId);
        if (!$("#slug-field").hasClass("d-none")) {
            window.form.appendHiddenInput("#category-form", "_method", "put");

            $("#category-form").attr(
                "action",
                `${window.Korf.baseUrl}/admin/categories/${selectedId}`
            );
        }

        e.currentTarget.submit();
    }

    removeSubmitButtonOffsetOn(tabs, tabsSelector = null) {
        tabs = Array.isArray(tabs) ? tabs : [tabs];

        $(tabsSelector).on("click", (e) => {
            if (tabs.includes(e.currentTarget.getAttribute("href"))) {
                setTimeout(() => {
                    $("button[type=submit]")
                        .parent()
                        .removeClass("col-md-offset-3");
                }, 150);
            } else {
                setTimeout(() => {
                    $("button[type=submit]")
                        .parent()
                        .addClass("col-md-offset-3");
                }, 150);
            }
        });
    }
}
