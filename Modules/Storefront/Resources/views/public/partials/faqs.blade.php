@use('Spatie\SchemaOrg\Schema')
@if($faqs && $faqs->isNotEmpty())
<div class="row">
    <div class="col-sm-12">
        <div class="container-module module-articles faqs py-5">
            <div class="title-module rc-mod">
                <span>{{ trans('storefront::layouts.faq_title') }}</span>
            </div>

            <div class="faq row">
                @foreach($faqs->split(2) as $chunk)
                    <div class="accordion-container col-sm-12 col-md-6 px-2">
                        @foreach($chunk as $faq)
                            @php
                                $isActive = $loop->parent->first && $loop->first;
                            @endphp

                            <div class="accordion-item-custom {{ $isActive ? 'accordion-active' : '' }} mb-3">
                                <div class="accordion__intro d-flex justify-content-between align-items-center p-3">
                                    <span>{!! $faq->question !!}</span>
                                    <div class="accordion__intro-btn">
                                        <svg class="icon icon-22 plus"><use xlink:href="#plus"></use></svg>
                                        <svg class="icon icon-22 minus"><use xlink:href="#minus"></use></svg>
                                    </div>
                                </div>
                                <div class="accordion__content p-3 pt-0">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('schema')
    {!! Schema::fAQPage()->mainEntity(
        $faqs->map(fn($faq) => Schema::question()
            ->name("➕ " . strip_tags($faq->question))
            ->acceptedAnswer(Schema::answer()->text("ᐈ " . $faq->answer))
        )->toArray()
    )->toScript() !!}
@endpush
@endif
