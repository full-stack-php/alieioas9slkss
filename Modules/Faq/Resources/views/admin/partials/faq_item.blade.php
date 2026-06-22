@php
    $idKey = $faq_index;
    $inputBase = 'faqs[' . $idKey . ']';
@endphp

<div class="card mb-3 faq-item border-primary border" id="faq-item-{{ $idKey }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            {{ trans('faq::faq.item') }} #{{ is_numeric($faq_index) ? $faq_index + 1 : $faq_index }}
            @if (!$is_new && isset($faq->id))
                <small class="text-muted">(ID: {{ $faq->id }})</small>
            @endif
        </h5>
        <button type="button" class="btn btn-link remove-faq-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10" opacity="0.5"/><path stroke-linecap="round" d="m14.5 9.5l-5 5m0-5l5 5"/></g></svg>
        </button>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#faq-{{ $idKey }}-{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        {{ $language['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="faq-{{ $idKey }}-{{ $locale }}">

                    {{ Form::text($inputBase . '[' . $locale . '][question]', trans('faq::attributes.question'), $errors, $faq ?? null, [ 'labelCol' => 2, 'required' => true, 'placeholder' => trans('faq::attributes.question') ]) }}

                    {{ Form::textarea($inputBase . '[' . $locale . '][answer]', trans('faq::attributes.answer'),  $errors, $faq ?? null,  [ 'labelCol' => 2, 'required' => true, 'placeholder' => trans('faq::attributes.answer') ]) }}
                </div>
            @endforeach
        </div>

    </div>
</div>
