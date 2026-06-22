<div class="permission-row">
    <div class="row">
        <div class="col-md-5 col-sm-4">
            <span class="permission-label">{{ trans($permissionLabel) }}</span>
        </div>

        <div class="col-md-7 col-sm-8 d-flex justify-content-end">
            <div class="row">
                <div class="radio-btn clearfix">

                    @if (! is_null($entity))
                        @php
                            $permissionValue = old('permissions')["{$group}.{$permissionAction}"] ?? permission_value($entity->permissions ?: [], "{$group}.{$permissionAction}")
                        @endphp
                    @endif

                        <div class="form-check form-check-inline">
                            <input type="radio" value="1" id="{{ "{$group}-{$permissionAction}" }}-allow" class="form-check-input permission-allow" name="permissions[{{ "{$group}.{$permissionAction}" }}]" {{ isset($permissionValue) && $permissionValue == 1 ? 'checked' : '' }}>

                            <label class="form-check-label" for="{{ "{$group}-{$permissionAction}" }}-allow">{{ trans('user::roles.permissions.allow') }}</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" value="-1" id="{{ "{$group}-{$permissionAction}" }}-deny" class="form-check-input permission-deny" name="permissions[{{ "{$group}.{$permissionAction}" }}]" {{ isset($permissionValue) && $permissionValue == -1 ? 'checked' : '' }}>

                            <label class="form-check-label" for="{{ "{$group}-{$permissionAction}" }}-deny">{{ trans('user::roles.permissions.deny') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" value="0" id="{{ "{$group}-{$permissionAction}" }}-inherit" class="form-check-input permission-inherit" name="permissions[{{ "{$group}.{$permissionAction}" }}]" {{ isset($permissionValue) && $permissionValue == 0 ? 'checked' : '' }}>

                            <label class="form-check-label" for="{{ "{$group}-{$permissionAction}" }}-inherit">{{ trans('user::roles.permissions.inherit') }}</label>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
