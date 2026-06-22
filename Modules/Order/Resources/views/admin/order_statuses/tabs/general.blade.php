<ul class="nav nav-pills">
    @foreach (supported_locales() as $locale => $language)
        <li class="nav-item">
            <a href="#descriptionTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                <span class="d-none d-sm-block">{{ $language['name'] }}</span>
            </a>
        </li>
    @endforeach
</ul>
<div class="tab-content pt-2 text-muted">
    @foreach (supported_locales() as $locale => $language)
        <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="descriptionTabs{{ $locale }}">
            {{ Form::text( $locale . '[' . 'name' . ']', trans('order::statuses.attributes.name'), $errors, $order_status, ['labelCol' => 2, 'required' => true]) }}
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group {{ $errors->has('color') ? 'has-error' : '' }}">
            <label for="color" class="control-label text-left">
                {{ trans('order::statuses.attributes.color') }} <span class="text-red">*</span>
            </label>
            <div class="input-group colorpicker-element">
                <input type="color" name="color" id="color" class="form-control" style="padding: 2px 5px; height: 38px; width: 100%;" value="{{ old('color', $order_status->color ?? '#3c8dbc') }}">
            </div>
            {!! $errors->first('color', '<span class="help-block">:message</span>') !!}
        </div>

        <div class="form-group m-t-30">
            {{ Form::checkbox('is_active', trans('order::statuses.attributes.is_active'), trans('order::statuses.attributes.is_active'), $errors, $order_status) }}
        </div>
    </div>
</div>
