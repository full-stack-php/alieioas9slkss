<div class="modal-dialog chm-modal sm-modal-4 modal-dialog-centered">
    <div class="modal-content">
        <form id="login_data" method="post">
            @csrf

            <div class="modal-header">
                <div class="modal-title">
                    {{ trans('user::auth.login') }}
                </div>

                <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg class="icon icon-22">
                        <use xlink:href="#cross"></use>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group field_required">
                    <div class="input-group-flex">
                        <div class="input-group-icon">
                            <img src="{{ asset('build/assets/img/email-icon.svg') }}" alt="">
                        </div>

                        <input
                            type="text"
                            name="email"
                            value=""
                            placeholder="{{ trans('user::auth.enter_your_email') }}"
                            id="input-email-popup"
                            class="form-control"
                        >
                    </div>
                </div>

                <div class="form-group field_required">
                    <div class="input-group-flex">
                        <div class="input-group-icon">
                            <img src="{{ asset('build/assets/img/password-icon.svg') }}" alt="">
                        </div>

                        <input
                            type="password"
                            name="password"
                            value=""
                            placeholder="{{ trans('user::auth.enter_your_password') }}"
                            id="input-password-popup"
                            class="form-control"
                        >
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div class="register">
                            <a class="register" href="{{ route('register') }}">
                                {{ trans('user::auth.register') }}
                            </a>
                        </div>

                        <div class="forgotten-pass">
                            <a class="forgotten" href="{{ route('reset') }}">
                                {{ trans('user::auth.forgot_password') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember_me"
                        value="1"
                        id="remember-me-popup"
                    >

                    <label class="form-check-label" for="remember-me-popup">
                        {{ trans('user::auth.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-12 px-0">
                        <button
                            class="chm-btn chm-btn-primary chm-lg-rounded w-100"
                            type="button"
                            id="button-login-popup"
                            data-action="{{ route('login.modal.post') }}"
                        >
                            {{ trans('user::auth.sign_in') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
