import "../sass/media-picker.scss";
import {Modal} from "bootstrap";

export default class {
    constructor(options) {
        this.options = _.merge(
            {
                type: null,
                multiple: false,
                routePrefix: "file-manager",
                title: trans("media::media.file_manager.title"),
            },
            options
        );

        this.events = {};
        this.frame = this.getFrame();

        this.appendModalToBody();
        this.openFrame();
    }

    on(event, handler) {
        this.events[event] = handler;
    }

    getFrame() {
        let src = `${Korf.baseUrl}/admin/${this.options.routePrefix}?type=${this.options.type}&multiple=${this.options.multiple}`;

        return $(
            `<iframe class="file-manager-iframe" frameborder="0" src="${src}"></iframe>`
        );
    }

    appendModalToBody() {
        if ($(".media-picker-modal").length === 1) {
            return;
        }

        $("body").append(this.getModal());

        this.modalElement = document.querySelector(".media-picker-modal");
        this.mediaModalInstance = new Modal(this.modalElement);

        this.initCloseHandlers();
        this.modalElement.addEventListener('hidden.bs.modal', () => {
            this.mediaModalInstance.dispose();
            $(this.modalElement).remove();
        }, { once: true });
    }

    initCloseHandlers() {
        this.closeModalOnEsc();
        this.closeModalOnClickDismiss();
    }

    openFrame() {
        this.showModal();

        this.frame.on("load", () => {
            this.selectMedia();
        });
    }

    showModal() {
        this.mediaModalInstance.show();

        this.setFrameHeight();
        this.setFrameHeightOnWindowResize();
        this.setModalTitle($(".media-picker-modal"));
        this.setModalBody($(".media-picker-modal"));
    }

    setFrameHeight() {
        this.frame.css("height", window.innerHeight * 0.8);
    }

    setFrameHeightOnWindowResize() {
        window.addEventListener("resize", () => {
            this.setFrameHeight();
        });
    }

    setModalTitle(modal) {
        modal.find(".modal-title").text(this.options.title);
    }

    setModalBody(modal) {
        modal.find(".modal-body").html(this.frame);
    }

    closeModalOnEsc() {
        $(document).on("keydown", (e) => {
            if (e.key === "Escape") {
                this.mediaModalInstance.hide();
            }
        });

        // Обработчик для iframe
        this.frame.on("load", () => {
            this.frame.contents().on("keydown", (e) => {
                if (e.key === "Escape") {
                    this.mediaModalInstance.hide();
                }
            });
        });
    }

    closeModalOnClickDismiss() {
        const modal = $(".media-picker-modal");

        modal.find('[data-dismiss="modal"]').on("click", () => {
            this.mediaModalInstance.hide();
        });
    }


    selectMedia() {
        this.frame
            .contents()
            .find(".table")
            .on("click", ".select-media", (e) => {
                e.preventDefault();

                this.events["select"](e.currentTarget.dataset);

                if (this.options.multiple) {
                    $(e.currentTarget)
                        .attr("disabled", true)
                        .html(`<i class="fa fa-check" aria-hidden="true"></i>`);
                } else {
                    this.mediaModalInstance.hide();
                }
            });
    }

    getModal() {
        return `
            <div class="media-picker-modal modal fade" role="dialog" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModal" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content col-md-10 col-sm-11 clearfix">
                        <div class="row">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}
