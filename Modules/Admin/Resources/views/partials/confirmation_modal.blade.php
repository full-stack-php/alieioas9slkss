<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('admin::admin.delete.confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="default-message">
                    {{ $message ?? trans('admin::admin.delete.confirmation_message') }}
                </div>
            </div>

            <div class="modal-footer">
                <form method="POST" id="confirmation-form">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}

                    <button type="button" class="btn btn-default cancel" data-dismiss="modal">
                        {{ trans('admin::admin.buttons.cancel') }}
                    </button>

                    <button type="submit" class="btn btn-danger delete">
                        {{ trans('admin::admin.buttons.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
