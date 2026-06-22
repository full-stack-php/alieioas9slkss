@use('Spatie\SchemaOrg\Schema')
<div class="breadcrumb-box">
    <ul class="breadcrumb">
        <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
        @foreach($breadcrumbs as $crumb)
            @if($loop->last)
                <li><span>{{ $crumb->name }}</span></li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ url($crumb->getFullPath()) }}">{{ $crumb->name }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>

@php
    $listItems = [
        Schema::listItem()
            ->position(1)
            ->name(setting('storefront_schema_site_name') ?? 'Superlens')
            ->item(route('home'))
    ];

    $position = 2;
    foreach($breadcrumbs as $crumb) {
        $listItems[] = Schema::listItem()
            ->position($position)
            ->name($crumb->name)
            ->item(url($crumb->getFullPath()));

        $position++;
    }

    $breadcrumbSchema = Schema::breadcrumbList()
        ->itemListElement($listItems);
@endphp

@push('schema')
    {!! $breadcrumbSchema->toScript() !!}
@endpush
