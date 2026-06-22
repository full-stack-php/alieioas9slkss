<div class="row">
    <div class="col-md-12">
        {{ Form::text('new_from', trans('product::attributes.new_from'), $errors, $product, ['class' => 'datetime-picker']) }}
        {{ Form::text('new_to', trans('product::attributes.new_to'), $errors, $product, ['class' => 'datetime-picker'] ) }}
    </div>
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#noticeTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="noticeTabs{{ $locale }}">
                    {{ Form::text( $locale . '[' . 'notice_message' . ']', trans('product::attributes.notice'), $errors, $product, ['labelCol' => 2, 'required' => false]) }}
                </div>
            @endforeach
        </div>
    </div>
</div>
