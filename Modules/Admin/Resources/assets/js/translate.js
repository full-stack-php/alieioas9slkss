

export default function (options = {}) {
    function parseFieldName(fieldName) {
        // Паттерн для поиска: faqs[0][uk][question] или uk[name]
        const regex = /(\w+)\[(\w+)\]\[(\w+)\]\[(\w+)\]|(\w+)\[(\w+)\]/;
        const match = fieldName.match(regex);

        if (!match) {
            return null;
        }

        // Если это faqs[0][uk][question]
        if (match[1]) {
            return {
                isTranslatable: true,
                locale: match[3],
                base: `${match[1]}[${match[2]}]`, // faqs[0]
                attribute: match[4] // question
            };
        }
        // Если это uk[name] (менее вложенный локализованный атрибут)
        if (match[5]) {
            return {
                isTranslatable: true,
                locale: match[5],
                base: '',
                attribute: match[6]
            };
        }

        return null;
    }

    // === 1. Функция добавления кнопок ===
    function appendTranslateButtons() {
        // Выбираем все label внутри формы, которые содержат квадратные скобки (признак локализации)
        $('form label[for*="["]').not('.translate-processed').each(function () {
            const $label = $(this);
            const fieldName = $label.attr('for');

            const parsed = parseFieldName(fieldName);

            if (parsed && parsed.isTranslatable) {
                const targetLocale = parsed.locale;

                const $button = $(`<button type="button"
                                    class="btn btn-xs btn-outline-info translate-btn ms-2"
                                    data-locale="${targetLocale}"
                                    data-field-name="${fieldName}"
                                    title="Translate">
                                    <iconify-icon icon="solar:planet-2-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                            </button>`);

                // Добавляем кнопку после текста label
                $label.after($button);

                // Отмечаем, что этот label уже обработан
                $label.addClass('translate-processed');
            }
        });
    }

    // === 2. Логика перевода ===
    function setupTranslationLogic() {
        // 2.1. Обработчик клика
        $(document).on('click', '.translate-btn', function (e) {
            e.preventDefault();

            const $button = $(this);
            const fullFieldName = $button.data('field-name');

            const parsed = parseFieldName(fullFieldName);
            if (!parsed || !parsed.isTranslatable) return;

            const currentLocale = parsed.locale;
            const $sourceField = $(`[name="${fullFieldName}"]`);
            const sourceText = $sourceField.val();

            if (!sourceText.trim()) {
                alert('Исходное поле должно быть заполнено для перевода.');
                return;
            }

            const allLocales = ['ru', 'uk']; // Замените на ваши локали
            const targetLocale = allLocales.find(lang => lang !== currentLocale);

            if (!targetLocale) return;

            const targetFieldName = fullFieldName.replace(`[${currentLocale}]`, `[${targetLocale}]`);
            const $targetField = $(`[name="${targetFieldName}"]`);

            if ($targetField.length === 0) {
                alert(`Не найдено поле для перевода: ${targetFieldName}`);
                return;
            }
            $button.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

        });
    }

    // === 3. Запуск при загрузке и при добавлении нового FAQ ===

    $(document).ready(function () {
        appendTranslateButtons();
        setupTranslationLogic();

        // Перехватываем клик по кнопке "Добавить Вопрос" (если она имеет ID 'add-faq-btn')
        $('#add-faq-btn').on('click', function () {
            // Небольшая задержка, чтобы DOM успел обновиться
            setTimeout(appendTranslateButtons, 50);
        });
    });
}
