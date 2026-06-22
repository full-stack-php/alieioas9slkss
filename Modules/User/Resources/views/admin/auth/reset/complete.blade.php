@extends('user::admin.auth.layout')

@section('title', trans('user::auth.reset_password'))

@section('content')

    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="{{ route('home') }}" class="logo-dark" title="{{ trans('user::auth.back_to_home') }}">
                                        <img src="./backoffice/assets/logo-dark.png" height="24" alt="logo dark">
                                    </a>

                                    <a href="{{ route('home') }}" class="logo-light" title="{{ trans('user::auth.back_to_home') }}">
                                        <img src="./backoffice/assets/logo-light.png" height="24" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">{{ trans('user::auth.create_new_password') }}</h2>

                                <p class="text-muted mt-1 mb-4">{{ trans('user::auth.this_password_should_be_different') }}</p>

                                <div>
                                    <form class="authentication-form" method="POST"
                                          action="{{ route('admin.reset.complete.post', ['email' => $email ?? request()->route('email'), 'code' => $code ?? request()->route('code')]) }}">

                                        {{ csrf_field() }}
                                        <div class="mb-3">
                                            <label class="form-label" for="new_password">{{ trans('user::auth.password') }}</label>
                                            <input type="text" id="new_password" name="new_password"
                                                   placeholder="{{ trans('user::attributes.users.new_password') }}"
                                                   class="form-control">
                                            {!! $errors->first('new_password',  '<p class="text-danger mb-3">:message</p>') !!}
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="new_password_confirmation">{{ trans('user::auth.confirm_password') }}</label>
                                            <input type="text" id="new_password_confirmation"  name="new_password_confirmation"
                                                   placeholder="{{ trans('user::attributes.users.confirm_new_password') }}"
                                                   class="form-control">
                                            {!! $errors->first('new_password_confirmation',  '<p class="text-danger mb-3">:message</p>') !!}
                                        </div>
                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-primary" type="submit">{{ trans('user::auth.reset_password') }}</button>
                                        </div>
                                    </form>
                                </div>

                                <p class="mt-5 text-danger text-center"><a href="{{ route('admin.login') }}" class="text-dark fw-bold ms-1">{{ trans('user::auth.back_to_login') }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="./backoffice/assets/small/img-10.jpg" alt="" class="w-100 h-100">
                        </div>
                    </div> <!-- end card -->
                </div>
            </div>
        </div>
    </div>
@endsection
