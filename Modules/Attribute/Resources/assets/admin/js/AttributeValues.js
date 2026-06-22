export default class {
    constructor() {
        this.attributeId = 0;
        this.valuesCount = 0;

        console.log(Korf.data["attribute.values"]);
        this.addOldValues(Korf.data["attribute.values"]);

        if (this.valuesCount === 0) {
            this.addAttributeValue();
        }

        this.eventListeners();
        this.sortable();

        window.admin.removeSubmitButtonOffsetOn("#values");
    }

    addOldValues(values = {}) {
        const supportedLocales = Korf.data['supported_locales'] || [];

        for (let value of values) {
            if (!value.translations) {
                value.translations = [];
                supportedLocales.forEach(locale => {
                    if (value[locale] && value[locale].value !== undefined) {
                        value.translations.push({
                            locale: locale,
                            value: value[locale].value
                        });
                    }
                });
            }
            this.addAttributeValue(value);
        }
    }

    addAttributeValue(value = { id: "", translations: [] }) {
        let template = _.template($("#attribute-value-template").html());
        // Передаем объект ошибок в шаблон
        let html = template({
            valueId: this.valuesCount++,
            value,
            errors: Korf.data['errors'] || {}
        });

        $("#attribute-values").append(html);
    }

    eventListeners() {
        $("#add-new-value").on("click", () => this.addAttributeValue());

        $("#attribute-values").on("click", ".delete-row", (e) => {
            $(e.currentTarget).closest("tr").remove();
        });
    }

    sortable() {
        Sortable.create(document.getElementById("attribute-values"), {
            handle: ".drag-handle",
            animation: 150,
        });
    }
}
