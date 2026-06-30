@php
    $errorBase = 'videos.' . $index;

    $hasRowErrors = collect($errors->getMessages())
        ->keys()
        ->contains(function ($key) use ($errorBase) {
            return str_starts_with($key, $errorBase . '.');
        });

    $titleErrorKey = "{$errorBase}.title";
    $urlErrorKey = "{$errorBase}.url";
    $sortOrderErrorKey = "{$errorBase}.sort_order";

    $youtubeId = data_get($video, 'youtube_id');
    $thumbnailUrl = data_get($video, 'thumbnail_url');
    $videoTitle = data_get($video, 'title', '');
@endphp

<tr class="{{ $hasRowErrors ? 'has-error' : '' }}">
    <td class="text-center align-middle">


        <input
            type="radio"
            name="main_video"
            value="{{ $index }}"
            @checked($isMain)
        >
    </td>

    <td>
        <input
            type="hidden"
            name="videos[{{ $index }}][id]"
            value="{{ data_get($video, 'id', '') }}"
        >

        <input
            type="text"
            name="videos[{{ $index }}][title]"
            class="form-control {{ $errors->has($titleErrorKey) ? 'is-invalid' : '' }}"
            value="{{ old("videos.{$index}.title", data_get($video, 'title', '')) }}"
            placeholder="{{ trans('product::products.form.video.title') }}"
        >

        @if($errors->has($titleErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($titleErrorKey) }}
            </div>
        @endif
    </td>

    <td>
        <input
            type="text"
            name="videos[{{ $index }}][url]"
            class="form-control {{ $errors->has($urlErrorKey) ? 'is-invalid' : '' }}"
            value="{{ old("videos.{$index}.url", data_get($video, 'url', '')) }}"
            placeholder="{{ trans('product::products.form.video.url_placeholder') }}"
        >

        @if($errors->has($urlErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($urlErrorKey) }}
            </div>
        @endif

        @if($youtubeId && $thumbnailUrl)
            <div class="mt-2">
                <img
                    src="{{ $thumbnailUrl }}"
                    alt="{{ $videoTitle }}"
                    style="max-width: 160px; height: auto;"
                >
            </div>
        @endif
    </td>

    <td>
        <input
            type="number"
            name="videos[{{ $index }}][sort_order]"
            class="form-control {{ $errors->has($sortOrderErrorKey) ? 'is-invalid' : '' }}"
            value="{{ old("videos.{$index}.sort_order", data_get($video, 'sort_order', 0)) }}"
            min="0"
        >

        @if($errors->has($sortOrderErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($sortOrderErrorKey) }}
            </div>
        @endif
    </td>

    <td class="text-center align-middle">
        <button type="button" class="btn btn-danger btn-sm btn-delete-product-video">
            {{ trans('product::products.form.video.delete') }}
        </button>
    </td>
</tr>
