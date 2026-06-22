<!DOCTYPE html>
<html lang="{{ locale() }}">
    <head>
        <base href="{{ url('/') }}">
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            {{trans('admin::admin.admin_panel')}} - @yield('title')
        </title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


        @stack('styles')


        @vite(['Modules/Admin/Resources/assets/scss/icons.scss', 'Modules/Admin/Resources/assets/scss/app.scss'])


        @vite(['Modules/Admin/Resources/assets/js/config.js'])

        @vite(['Modules/Admin/Resources/assets/js/app.js','Modules/Admin/Resources/assets/js/layout.js'])

        @include('admin::partials.globals')
    </head>

    <body class="">




        <div class="wrapper">
            @include('admin::partials.top_nav')
            @include('admin::partials.sidebar')


            <div class="page-content">
                <div class="container-fluid">
                    @yield('content_header')
                    @include('admin::partials.notification')
                    @yield('content')
                    @include('admin::partials.footer')
                </div>
            </div>
        </div>


        @include('admin::partials.confirmation_modal')



        @stack('scripts')
    </body>
</html>
