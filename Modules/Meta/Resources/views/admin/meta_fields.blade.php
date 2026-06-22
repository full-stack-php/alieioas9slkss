<ul class="nav nav-pills">
    @foreach (supported_locales() as $locale => $language)
        <li class="nav-item">
            <a href="#metaTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                <span class="d-none d-sm-block">{{ $language['name'] }}</span>
            </a>
        </li>
    @endforeach
</ul>
<div class="tab-content pt-2 text-muted">
@foreach (supported_locales() as $locale => $language)
    <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="metaTabs{{ $locale }}">
        {{ Form::text('meta[' . $locale . '][meta_title]', trans('meta::attributes.meta_title'), $errors, $entity?? null, ['labelCol' => 2]) }}
        {{ Form::textarea('meta[' . $locale . '][meta_description]', trans('meta::attributes.meta_description'), $errors, $entity?? null, ['labelCol' => 2]) }}
    </div>
@endforeach
</div>



@if ($entity->slug ?? false)
    {{ Form::text('slug', trans('page::attributes.slug'), $errors, $entity, ['required' => true]) }}
@endif
