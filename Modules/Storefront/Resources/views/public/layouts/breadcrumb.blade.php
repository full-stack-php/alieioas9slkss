@hasSection('breadcrumb')
    <div class="breadcrumb-box">
        <ul class="breadcrumb">
            <li>
                <a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a>
            </li>

            @yield('breadcrumb')
        </ul>
    </div>
@endif
