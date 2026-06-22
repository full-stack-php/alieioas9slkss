<div class="modal-dialog chm-modal sm-modal-4 modal-dialog-centered">
    <div id="popup-callback" class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                {{ trans('storefront::contact.callback_title') }}
            </div>

            <button type="button" class="close-modal" data-bs-dismiss="modal">
                <svg class="icon icon-11"><use xlink:href="#cross"></use></svg>
            </button>
        </div>

        <div class="modal-body">
            <form id="callback_data" method="post">
                @csrf
                @honeypot

                <input type="hidden" id="callback_url" value="" name="url_site">

                <div class="form-group field_required">
                    <div class="input-group-flex">
                        <div class="input-group-icon">
                            <img src="{{ asset('storage/media/name-icon.svg') }}" alt="">
                        </div>

                        <input
                            id="callback-name"
                            class="form-control callback-name"
                            type="text"
                            placeholder="{{ trans('storefront::contact.contact_placeholder_name') }}"
                            name="name"
                        >
                    </div>
                </div>

                <div class="form-group field_required">
                    <div class="input-group-flex">
                        <div class="input-group-icon">
                            <img src="{{ asset('storage/media/tel-icon.svg') }}" alt="">
                        </div>

                        <input
                            id="callback-phone"
                            class="form-control callback-phone"
                            type="tel"
                            placeholder="{{ trans('storefront::contact.contact_placeholder_phone') }}"
                            name="phone"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-flex">
                        <input
                            id="callback-email"
                            class="form-control callback-email"
                            type="email"
                            placeholder="Email"
                            name="email_buyer"
                        >

                        <div class="input-group-icon">
                            <img src="{{ asset('storage/media/email-icon.svg') }}" alt="">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <textarea
                        id="callback-comment"
                        rows="3"
                        class="form-control callback-comment"
                        placeholder="{{ trans('storefront::contact.message') }}"
                        name="comment_buyer"
                    ></textarea>
                </div>

                <div class="form-group">
                    <input
                        type="text"
                        name="time_callback_on"
                        value=""
                        class="form-control callback-datetime"
                        placeholder="{{ trans('storefront::contact.when_you_call_back') }}"
                    >
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button
                id="up-btn-callback"
                class="chm-btn chm-btn-primary chm-px-lg chm-lg-rounded w-100"
                type="button"
            >
                {{ trans('storefront::contact.send_message') }}
            </button>
        </div>
    </div>
</div>
