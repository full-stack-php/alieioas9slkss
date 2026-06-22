@extends('user::admin.auth.layout')

@section('title', trans('user::auth.login'))

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

                                <h2 class="fw-bold fs-24">{{ trans('user::auth.welcome') }}</h2>

                                <p class="text-muted mt-1 mb-4">{{ trans('user::auth.enter_your_details') }}</p>

                                @include('user::admin.partials.notification')


                                <div class="mb-5">
                                    <form method="POST" action="{{ route('admin.login.post') }}" class="authentication-form">
                                        {{ csrf_field() }}


                                        <div class="mb-3">
                                            <label class="form-label" for="email">{{ trans('user::auth.email') }}</label>
                                            <input type="text"
                                                   id="email"
                                                   name="email"
                                                   class="form-control bg-"
                                                   placeholder="Enter your email"
                                                   value="{{ old('email') }}"
                                                   autofocus
                                            >
                                            {!! $errors->first('email', '<p class="text-danger mb-3">:message</p>') !!}
                                        </div>

                                        <div class="mb-3">
                                            <a href="{{ route('admin.reset') }}" class="float-end text-muted text-unline-dashed ms-1"> {{ trans('user::auth.forgot_password') }}</a>

                                            <label class="form-label" for="password">{{ trans('user::auth.password') }}</label>
                                            <input type="password" id="password" class="form-control"
                                                   placeholder="{{ trans('user::auth.enter_your_password') }}" name="password" value="">

                                            {!! $errors->first('password', '<p class="text-danger mb-3">:message</p>') !!}
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="remember_me" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">{{ trans('user::attributes.auth.remember_me') }}</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">{{ trans('user::auth.sign_in') }}</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="./backoffice/assets/small/img-10.jpg" alt="" class="w-100 h-100">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
