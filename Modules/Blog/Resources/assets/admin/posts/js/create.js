import Alpine from "alpinejs";
import tinyMCE from "@admin/js/wysiwyg";
import Errors from "@admin/js/Errors";

window.Alpine = Alpine;

let textEditor;
let tagsSelect;

Alpine.data("postCreate", () => ({
    formSubmitting: false,
    formSubmissionType: null,
    form: {
        meta: {},
        featured_image: {},
        publish_status: "published",
        blog_category_id: "",
    },
    errors: new Errors(),

    init() {
        textEditor = this.initTinyMce();
    },

    initTinyMce() {
        return tinyMCE({
            setup: (editor) => {
                editor.on("change", () => {
                    editor.save();
                    editor.getElement().dispatchEvent(new Event("input"));

                    this.errors.clear("description");
                });
            },
        });
    },

    focusDescriptionField() {
        textEditor.get("description").focus();
    },

    addFeaturedImage() {
        const picker = new MediaPicker({ type: "image" });

        picker.on("select", ({ id, path }) => {
            this.form.featured_image = {
                id: +id,
                path,
            };
        });
    },

    removeFeaturedImage() {
        this.form.featured_image = {};
    },

    focusFirstErrorField(formElements) {
        const errorKeys = Object.keys(this.errors.errors);

        // 💡 ИСПРАВЛЕНИЕ: Ищем поле по полному имени ошибки (например, uk.name -> uk[name])
        const firstErrorKey = errorKeys.find(key => key.includes('.'));

        if (!firstErrorKey) {
            // Если ошибки без префикса (как 'description' или 'blog_category_id'), используем старую логику
            const simpleErrorKey = errorKeys.find(key => !key.includes('.'));
            if (simpleErrorKey) {
                const firstErrorField = [...formElements].find((element) => element.name === simpleErrorKey);
                if (firstErrorField) {
                    this.handleFocus(firstErrorField);
                }
            }
            return;
        }

        // Преобразуем Laravel-ключ ошибки (uk.name) в HTML-имя (uk[name])
        const htmlName = firstErrorKey.replace('.', '[').concat(']');

        const firstErrorField = [...formElements].find((element) => {
            return element.name === htmlName;
        });

        if (firstErrorField) {
            this.handleFocus(firstErrorField);
        }
    },

    // 💡 Новый вспомогательный метод для фокусировки
    handleFocus(element) {
        if (element.classList.contains("wysiwyg")) {
            // Переключаем вкладку на нужный язык перед фокусировкой TinyMCE
            const locale = element.name.match(/(\w+)\[/)[1];
            const tabLink = document.querySelector(`a[href="#descriptionTabs${locale}"]`);

            if (tabLink) {
                tabLink.click(); // Переключаем вкладку
            }

            // Фокусируем TinyMCE
            textEditor.get(element.getAttribute("name")).focus();
            return;
        }

        element.focus();
    },

    resetForm() {
        this.errors.reset();

        this.form = {
            meta: {},
            featured_image: {},
            publish_status: "published",
            blog_category_id: "",
        };

        textEditor.get("description").setContent("");
        textEditor.get("description").execCommand("mceCancel");
        tagsSelect[0].selectize.clear();
    },
    collectFormData(formElement) {
        const arrayData = $(formElement).serializeArray();
        const finalData = {};
        const locales = window.Korf.supportedLocales;
        locales.forEach(locale => {
            finalData[locale] = {};
            finalData.meta = finalData.meta || {};
            finalData.meta[locale] = {};
        });

        locales.forEach(locale => {
            finalData[locale].name = $(`[name="${locale}[name]"]`).val() || '';
            finalData[locale].h1_name = $(`[name="${locale}[h1_name]"]`).val() || '';
            finalData[locale].description = $(`[name="${locale}[description]"]`).val() || '';

            // 💡 Мета-теги (meta_title, meta_description)
            finalData.meta[locale].meta_title = $(`[name="meta[${locale}][meta_title]"]`).val() || '';
            finalData.meta[locale].meta_description = $(`[name="meta[${locale}][meta_description]"]`).val() || '';
        });

        arrayData.forEach(item => {
            if (!locales.some(locale => item.name.startsWith(locale + '[')) && !item.name.startsWith('meta[')) {
                finalData[item.name] = item.value;
            }
        });


        return finalData; // Возвращаем объединенный объект
    },
    handleSubmit({ submissionType }) {
        this.formSubmitting = true;
        this.formSubmissionType = submissionType;

        // 1. Собираем все данные из формы (переводы, мета, простые поля)
        const dataToSend = this.collectFormData(this.$refs.form);

        // 2. Добавляем данные, которые хранятся только в Alpine-модели (ID файлов)
        dataToSend.files = {
            featured_image: this.form.featured_image.id
                ? [this.form.featured_image.id]
                : [],
        };


        axios
            .post(
                "/blog/posts",
                dataToSend, // 💡 ОТПРАВЛЯЕМ ОБЪЕДИНЕННЫЕ ДАННЫЕ
                {
                    params: {
                        ...((submissionType === "save_and_edit" ||
                            submissionType === "save_and_exit") && {
                            exit_flash: true,
                        }),
                    },
                }
            )
            .then(({ data }) => {
                if (this.formSubmissionType === "save_and_edit") {
                    location.href = `/admin/blog/posts/${data.blog_post_id}/edit`;

                    return;
                }

                if (this.formSubmissionType === "save_and_exit") {
                    location.href = "/admin/blog/posts";

                    return;
                }

                success(data.message);

                this.resetForm();
                this.$refs.form.elements[0].focus();
            })
            .catch(({ response }) => {
                if(response.data.errors){
                    this.errors.record(response.data.errors);
                    this.focusFirstErrorField(this.$refs.form.elements);
                }

                // error(response.data.message);
            })
            .finally(() => {
                this.formSubmitting = false;
                this.formSubmissionType = null;
            });
    },
}));

Alpine.start();
