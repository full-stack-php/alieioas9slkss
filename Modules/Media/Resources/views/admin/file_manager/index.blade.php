<!DOCTYPE html>
<html lang="{{ locale() }}">
    <head>
        <meta charset="UTF-8">

        <title>{{ trans('media::media.file_manager.title') }}</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        @vite([
            'Modules/Admin/Resources/assets/scss/icons.scss',
            'Modules/Admin/Resources/assets/scss/app.scss',
            'Modules/Media/Resources/assets/admin/sass/main.scss',
            'Modules/Admin/Resources/assets/js/config.js',
            'Modules/Admin/Resources/assets/js/app.js',
            'Modules/Admin/Resources/assets/js/layout.js',
            'Modules/Media/Resources/assets/admin/js/main.js'
            ])


        @include('admin::partials.globals')
    </head>

    <body class="file-manager">
        <div class="wrapper">
            <div class="page-content pt-5">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">
                                    @include('media::admin.media.partials.uploader')
                                    @include('media::admin.media.partials.table')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="notification-toast"></div>

        @include('admin::partials.confirmation_modal')

        <script type="module">
            console.log('DataTable');
            DataTable.set('.file-manager .table', {
                routePrefix: 'media',
                routes: {
                    table: {
                        name: 'table',
                        params: { type: '{{ $type }}' }
                    },
                    destroy: 'destroy',
                }
            });

            new DataTable('.file-manager .table', {
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                    { data: 'id', width: '5%' },
                    { data: 'thumbnail', orderable: false, searchable: false, width: '10%' },
                    { data: 'filename', name: 'filename' },
                    { data: 'created', name: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ],
            });
        </script>
    </body>
</html>
