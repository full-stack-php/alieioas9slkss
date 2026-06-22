<div class="row">
    <div class="col-lg-12 col-md-12 mb-3">
        <div class="btn-group permission-parent-actions pull-right">
            <button type="button" class="btn btn-success allow-all">{{ trans('user::roles.permissions.allow_all')}}</button>
            <button type="button" class="btn btn-danger deny-all">{{ trans('user::roles.permissions.deny_all')}}</button>
            <button type="button" class="btn btn-info inherit-all">{{ trans('user::roles.permissions.inherit_all')}}</button>
        </div>
    </div>
</div>

@foreach ($permissions as $module => $modulePermissions)
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card border-secondary border">
                <div class="card-header">
                    <h5 class="card-title mb-1 text-success">{{ $module }}</h5>
                </div>
                <div class="card-body">

                    @foreach ($modulePermissions as $group => $groupPermissions)
                        <div class="permission-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="permission-group-head">
                                        <div class="row mb-1">
                                            <div class="col-md-4 col-sm-4 d-flex justify-content-start align-items-center">
                                                <h6>{{ $group }}</h6>
                                            </div>

                                            <div class="col-md-8 col-sm-8 d-flex justify-content-end">
                                                <div class="btn-group permission-group-actions pull-right">
                                                    <button type="button" class="btn btn-success allow-all">{{ trans('user::roles.permissions.allow_all')}}</button>
                                                    <button type="button" class="btn btn-danger deny-all">{{ trans('user::roles.permissions.deny_all')}}</button>
                                                    <button type="button" class="btn btn-info inherit-all">{{ trans('user::roles.permissions.inherit_all')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        @foreach ($groupPermissions as $permissionAction => $permissionLabel)
                                            @include('user::admin.partials.permissions.actions')
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
