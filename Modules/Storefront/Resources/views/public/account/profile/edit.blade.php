@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_profile'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_profile') }}</li>
@endsection

@section('panel')
    <div class="panel">
        <div class="panel-header">
            <h3 class="fw-semibold">{{ trans('storefront::account.profile.account') }}</h3>
        </div>

        <div class="panel-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="my-profile">
                <form method="POST" action="{{ route('account.profile.update') }}">
                    @csrf
                    @method('put')

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group field_required">
                                <div class="input-group-flex">
                                    <input type="email" name="email" placeholder="{{ trans('storefront::account.profile.email') }}" value="{{ old('email', $account->email) }}" id="email" class="form-control @error('email') error_input @enderror" required>
                                </div>

                                @error('email')
                                <div class="us-text-error">{{ $message }}</div>
                                <div class="us-error-icon">
                                    <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group field_required">
                                <div class="input-group-flex">
                                    <div class="input-group-flex">
                                        <input type="tel" name="phone" placeholder="{{ trans('storefront::account.profile.phone') }}" value="{{ old('phone', $account->phone) }}" id="phone" class="form-control @error('phone') error_input @enderror" required>
                                    </div>

                                    @error('phone')
                                    <div class="us-text-error">{{ $message }}</div>
                                    <div class="us-error-icon">
                                        <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                    </div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group field_required">
                                <div class="input-group-flex">

                                    <div class="input-group-flex">
                                        <input type="text" name="first_name" placeholder="{{ trans('storefront::account.profile.first_name') }}" value="{{ old('first_name', $account->first_name) }}" id="first-name" class="form-control @error('first_name') error_input @enderror" required>
                                    </div>

                                    @error('first_name')
                                    <div class="us-text-error">{{ $message }}</div>
                                    <div class="us-error-icon">
                                        <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group field_required">
                                <div class="input-group-flex">

                                    <div class="input-group-flex">
                                        <input type="text" name="last_name" placeholder="{{ trans('storefront::account.profile.last_name') }}" value="{{ old('last_name', $account->last_name) }}" id="last-name" class="form-control @error('last_name') error_input @enderror" required>
                                    </div>

                                    @error('last_name')
                                    <div class="us-text-error">{{ $message }}</div>
                                    <div class="us-error-icon">
                                        <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <div class="input-group-flex">
                                    <div class="input-group-flex">
                                        <input type="password" placeholder="{{ trans('storefront::account.profile.password') }}" name="password" id="password" class="form-control @error('password') error_input @enderror">
                                    </div>

                                    @error('password')
                                    <div class="us-text-error">{{ $message }}</div>
                                    <div class="us-error-icon">
                                        <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <div class="input-group-flex">
                                    <label for="confirm-password">

                                    </label>

                                    <div class="input-group-flex">
                                        <input type="password" placeholder="{{ trans('storefront::account.profile.confirm_password') }}" name="password_confirmation" id="confirm-password" class="form-control @error('password_confirmation') error_input @enderror">
                                    </div>

                                    @error('password_confirmation')
                                    <div class="us-text-error">{{ $message }}</div>
                                    <div class="us-error-icon">
                                        <img class="success-icon" alt="error" src="/storage/media/error-icon.svg">
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg btn-primary btn-save-changes mt-3" data-loading>
                        {{ trans('storefront::account.profile.save_changes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('globals')
    @vite([
        'Modules/Storefront/Resources/assets/public/sass/pages/account/profile/main.scss',
    ])
@endpush
