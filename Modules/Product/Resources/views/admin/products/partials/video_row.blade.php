<tr>
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
            value="{{ $video->id ?? '' }}"
        >

        <input
            type="text"
            name="videos[{{ $index }}][title]"
            class="form-control"
            value="{{ old("videos.{$index}.title", $video->title ?? '') }}"
            placeholder="{{ trans('product::products.form.video.title') }}"
        >
    </td>

    <td>
        <input
            type="text"
            name="videos[{{ $index }}][url]"
            class="form-control"
            value="{{ old("videos.{$index}.url", $video->url ?? '') }}"
            placeholder="{{ trans('product::products.form.video.url_placeholder') }}"
        >

        @if($video && $video->youtube_id)
            <div class="mt-2">
                <img
                    src="{{ $video->thumbnail_url }}"
                    alt="{{ $video->title }}"
                    style="max-width: 160px; height: auto;"
                >
            </div>
        @endif
    </td>

    <td>
        <input
            type="number"
            name="videos[{{ $index }}][sort_order]"
            class="form-control"
            value="{{ old("videos.{$index}.sort_order", $video->sort_order ?? 0) }}"
            min="0"
        >
    </td>

    <td class="text-center align-middle">
        <button type="button" class="btn btn-danger btn-sm btn-delete-product-video">
            {{ trans('product::products.form.video.delete') }}
        </button>
    </td>
</tr>
