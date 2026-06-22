import DataTable from "datatables.net-bs";
import qs from "qs";
import {Modal} from 'bootstrap';
import axios from 'axios';

// Initialize state holders.
Korf.dataTable = { routePrefix: {}, routes: {}, selected: {} };

let table = null;

export default class {
    constructor(selector, options, callback) {
        this.selector = selector;
        this.element = $(selector);

        if (Korf.dataTable.selected[selector] === undefined) {
            Korf.dataTable.selected[selector] = [];
        }
        this.initiateDataTable(options, callback);
        this.addErrorHandler();
        this.registerTableProcessingPlugin();
    }

    initiateDataTable(options, callback) {
        let sortColumn = this.element.find("th[data-sort]");

        table = new DataTable(
            this.element,
            _.merge(
                {
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: this.route("table", { table: true }),
                        data: (d) => {
                            $(".js-filter").each(function () {
                                const name = $(this).attr("name");
                                const value = $(this).val();
                                if (value !== "" && value !== null) {
                                    d[name] = value;
                                }
                            });
                        }
                    },
                    stateSave: true,
                    sort: true,
                    info: true,
                    filter: true,
                    lengthChange: true,
                    paginate: true,
                    autoWidth: false,
                    pageLength: 20,
                    lengthMenu: [10, 20, 50, 100, 200],
                    order: [
                        sortColumn.index() !== -1 ? sortColumn.index() : 1,
                        sortColumn.data("sort") || "desc",
                    ],
                    layout: {
                        topEnd: {
                            search: {
                                placeholder: trans(
                                    "admin::admin.table.search_here"
                                ),
                            },
                        },
                    },
                    language: {
                        sInfo: trans(
                            "admin::admin.table.showing_start_end_total_entries"
                        ),
                        sInfoEmpty: trans(
                            "admin::admin.table.showing_empty_entries"
                        ),
                        sLengthMenu: trans(
                            "admin::admin.table.show_menu_entries"
                        ),
                        sInfoFiltered: trans(
                            "admin::admin.table.filtered_from_max_total_entries"
                        ),
                        sEmptyTable: trans(
                            "admin::admin.table.no_data_available_table"
                        ),
                        sLoadingRecords: trans("admin::admin.table.loading"),
                        sProcessing: trans("admin::admin.table.processing"),
                        sZeroRecords: trans(
                            "admin::admin.table.no_matching_records_found"
                        ),
                    },
                    initComplete: () => {
                        if (this.hasRoute("destroy")) {
                            let deleteButton = this.addTableActions();

                            deleteButton.on("click", () => this.deleteRows());

                            this.selectAllRowsEventListener();
                        }

                        $(".js-filter").on("change", () => {
                            table.draw();
                        });

                        if (this.hasRoute("create")) {
                            let createButton = this.addTableActionCreate();
                            createButton.on('click', () => window.open(this.route("create", {}), '_self'))
                        }

                        if (this.hasRoute("show") || this.hasRoute("edit")) {
                            this.onRowClick(this.redirectToRowPage);
                        }

                        if (callback !== undefined) {
                            callback.call(this);
                        }
                    },
                    rowCallback: (row, data) => {
                        if (this.hasRoute("show") || this.hasRoute("edit")) {
                            this.makeRowClickable(row, data.id);
                        }
                    },
                    drawCallback: () => {
                        this.element.find(".select-all").prop("checked", false);

                        setTimeout(() => {
                            this.selectRowEventListener();
                            this.checkSelectedCheckboxes(
                                this.constructor.getSelectedIds(this.selector)
                            );
                        });
                    },
                    stateSaveParams(settings, data) {
                        delete data.search;
                    },
                },
                options
            )
        );
    }

    addTableActions() {
        let button = `
            <button type="button" class="btn btn-soft-primary btn-delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M2.75 6.167c0-.46.345-.834.771-.834h2.665c.529-.015.996-.378 1.176-.916l.03-.095l.115-.372c.07-.228.131-.427.217-.605c.338-.702.964-1.189 1.687-1.314c.184-.031.377-.031.6-.031h3.478c.223 0 .417 0 .6.031c.723.125 1.35.612 1.687 1.314c.086.178.147.377.217.605l.115.372l.03.095c.18.538.74.902 1.27.916h2.57c.427 0 .772.373.772.834S20.405 7 19.979 7H3.52c-.426 0-.771-.373-.771-.833M11.607 22h.787c2.707 0 4.06 0 4.941-.863c.88-.864.97-2.28 1.15-5.111l.26-4.081c.098-1.537.147-2.305-.295-2.792s-1.187-.487-2.679-.487H8.23c-1.491 0-2.237 0-2.679.487s-.392 1.255-.295 2.792l.26 4.08c.18 2.833.27 4.248 1.15 5.112S8.9 22 11.607 22"/></svg>
                <span>${trans("admin::admin.buttons.delete")}</span>
            </button>
        `;

        return $(button).appendTo(
            this.element.closest(".dt-container").find(".dt-length")
        );
    }
    addTableActionCreate() {
        let button = `
            <button type="button" class="btn btn-soft-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10" opacity="0.5"/><path fill="currentColor" fill-rule="evenodd" d="M4.25 13a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-4.19l-6.72 6.72a.75.75 0 0 1-1.06-1.06l6.72-6.72H5a.75.75 0 0 1-.75-.75" clip-rule="evenodd"/></svg>
                <span>${trans("admin::admin.buttons.create")}</span>
            </button>
        `;

        return $(button).prependTo(
            this.element.closest(".dt-container").find(".dt-search")
        );
    }

    deleteRows() {
        let checked = this.element.find(".select-row:checked");

        if (checked.length === 0) {
            return;
        }

        const modalElement = document.getElementById("confirmation-modal");

        if (!modalElement) {
            console.error("Confirmation modal element not found!");
            return;
        }

        let confirmationModal = Modal.getInstance(modalElement);

        if (!confirmationModal) {
            confirmationModal = new Modal(modalElement);
        }

        let deleted = [];

        confirmationModal.show();

        $(modalElement).find("form")
            .off("submit")
            .on("submit", (e) => {
                e.preventDefault();

                // 5. Вызываем метод hide() вместо .modal("hide")
                confirmationModal.hide();

                let ids = this.constructor.getRowIds(checked);

                // ... (остальной код AJAX остается прежним)
                if (
                    deleted.length !== 0 &&
                    _.difference(deleted, ids).length === 0
                ) {
                    return;
                }

                // ... (логика AXIOS)
                axios
                    .delete(this.route("destroy", { ids: ids.join() }))
                    .then(() => {
                        deleted = _.flatten(deleted.concat(ids));

                        this.constructor.setSelectedIds(this.selector, []);
                        this.constructor.reload(this.element);
                    })
                    .catch((error) => {
                        // Ваша функция error(message)
                        error(error.response.data.message);

                        deleted = _.flatten(deleted.concat(ids));

                        this.constructor.setSelectedIds(this.selector, []);
                        this.constructor.reload(this.element);
                    });
            });
    }

    makeRowClickable(row, id) {
        let key = this.hasRoute("show") ? "show" : "edit";
        let url = this.route(key, { id });

        $(row).addClass("clickable-row").data("href", url);
    }

    onRowClick(handler) {
        let row = "tbody tr.clickable-row td";

        if (this.element.find(".select-all").length !== 0) {
            row += ":not(:first-child)";
        }

        this.element.on("click", row, function(e) {
            // if ($(e.target).closest('button').length > 0 || $(e.target).is('button')) {
            //     return;
            // }

            if (
                $(e.target).closest('a, button, input, select, textarea, label, .js-stop-row-click').length > 0
            ) {
                return;
            }

            handler(e);
        });
    }

    redirectToRowPage(e) {
        window.open(
            $(e.currentTarget).parent().data("href"),
            e.ctrlKey ? "_blank" : "_self"
        );
    }

    selectAllRowsEventListener() {
        this.element.find(".select-all").on("change", (e) => {
            this.element
                .find(".select-row")
                .prop("checked", e.currentTarget.checked);

            if (e.currentTarget.checked) {
                this.element.find(".clickable-row").addClass("active");
            } else {
                this.element.find(".clickable-row").removeClass("active");
            }
        });
    }

    selectRowEventListener() {
        this.element.find(".select-row").on("change", (e) => {
            if (e.currentTarget.checked) {
                this.appendToSelected(e.currentTarget.value);

                $(e.currentTarget).parents(".clickable-row").addClass("active");
            } else {
                this.removeFromSelected(e.currentTarget.value);

                $(e.currentTarget)
                    .parents(".clickable-row")
                    .removeClass("active");
            }
        });
    }

    appendToSelected(id) {
        id = parseInt(id);

        if (!Korf.dataTable.selected[this.selector].includes(id)) {
            Korf.dataTable.selected[this.selector].push(id);
        }
    }

    removeFromSelected(id) {
        id = parseInt(id);

        Korf.dataTable.selected[this.selector] = Korf.dataTable.selected[this.selector].filter(selectedId => {
            return selectedId !== id;
        });
    }

    checkSelectedCheckboxes(selectedIds) {
        let rows = this.element.find(".select-row");

        let checkableRows = rows.toArray().filter((row) => {
            return selectedIds.includes(parseInt(row.value));
        });

        $(checkableRows).prop("checked", true);
    }

    route(name, params) {
        let router = Korf.dataTable.routes[this.selector][name];

        const url = `${window.Korf.baseUrl}/admin/${
            Korf.dataTable.routePrefix[this.selector]
        }`;

        if (typeof router === "string") {
            router = { name: router, params };
        }

        router.params = _.merge(params, router.params);

        if (name === "table") {
            return `${url}/index/${name}?${qs.stringify(params)}`;
        }


        if (name === "create") {
            return `${url}/${name}`;
        }

        if (name === "edit") {
            return `${url}/${params.id}/${name}`;
        }

        if (router.name === "table") {
            return `${url}/index/${router.name}?${qs.stringify(router.params)}`;
        }

        if (router.name === "show") {
            return `${url}/${params.id}`;
        }

        if (router.name === "edit") {
            return `${url}/${params.id}/${name}`;
        }

        if (router.name === "destroy") {
            return `${url}/${params.ids}`;
        }
    }

    hasRoute(name) {
        return Korf.dataTable.routes[this.selector][name] !== undefined;
    }

    static set(selector, { routePrefix = null, routes = {} }) {
        Korf.dataTable.routePrefix[selector] = routePrefix;
        Korf.dataTable.routes[selector] = routes;
    }

    static setSelectedIds(selector, selected) {
        Korf.dataTable.selected[selector] = selected;
    }

    static getSelectedIds(selector) {
        return Korf.dataTable.selected[selector];
    }

    static reload(selector, callback, resetPaging = false) {
        table.ajax.reload(callback, resetPaging);
    }

    static getRowIds(rows) {
        return rows.toArray().reduce((ids, row) => {
            return ids.concat(row.value);
        }, []);
    }

    static removeLengthFields() {
        $(".dt-length select").remove();
    }

    addErrorHandler() {
        DataTable.ext.errMode = (settings, helpPage, message) => {
            this.element.html(message);
        };
    }

    // https://datatables.net/plug-ins/api/processing()
    registerTableProcessingPlugin() {
        DataTable.Api.register("processing()", function (show) {
            return this.iterator("table", function (ctx) {
                ctx.oApi._fnProcessingDisplay(ctx, show);
            });
        });
    }
}
