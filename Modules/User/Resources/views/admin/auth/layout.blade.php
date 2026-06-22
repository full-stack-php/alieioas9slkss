<!DOCTYPE html>
<html  class="h-100">
    <head>
        <base href="{{ url('/') }}">
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <title>
            @yield('title') - Admin Area
        </title>
        @vite(['Modules/Admin/Resources/assets/scss/icons.scss','Modules/Admin/Resources/assets/scss/app.scss'])
        @vite(['Modules/Admin/Resources/assets/js/config.js'])
        @stack('globals')
    </head>

    <body class="h-100">

        @yield('content')

        @yield('script')
        @yield('script-bottom')
        @vite(['Modules/Admin/Resources/assets/js/app.js','Modules/Admin/Resources/assets/js/layout.js'])

    </body>


</html>


