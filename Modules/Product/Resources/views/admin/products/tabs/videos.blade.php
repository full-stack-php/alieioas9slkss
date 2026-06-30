@php
    $oldVideos = old('videos');

    if ($oldVideos !== null) {
        $videos = collect($oldVideos)->map(function ($video) {
            return \Modules\Product\Entities\ProductVideo::makeFromFormData($video);
        });
    } else {
        $videos = $product->videos;
    }

    $oldMainVideo = old('main_video');
@endphp

<div id="product-videos-wrapper">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 70px;">{{ trans('product::products.form.video.main') }}</th>
            <th>{{ trans('product::products.form.video.title') }}</th>
            <th>{{ trans('product::products.form.video.url') }}</th>
            <th style="width: 120px;">{{ trans('product::products.form.video.sort_order') }}</th>
            <th style="width: 80px;"></th>
        </tr>
        </thead>

        <tbody id="product-videos-container">
        @forelse($videos as $index => $video)
            @include('product::admin.products.partials.video_row', [
                'index' => $index,
                'video' => $video,
                'isMain' => $oldMainVideo !== null
                    ? (string) $oldMainVideo === (string) $index
                    : (bool) $video->is_main,
            ])
        @empty
            @include('product::admin.products.partials.video_row', [
                'index' => 0,
                'video' => null,
                'isMain' => true,
            ])
        @endforelse
        </tbody>
    </table>

    <button type="button" class="btn btn-secondary btn-sm" id="add-product-video-btn">
        {{ trans('product::products.form.video.add_video') }}
    </button>
</div>

<template id="product-video-row-template">
    @include('product::admin.products.partials.video_row', [
        'index' => '__INDEX__',
        'video' => null,
        'isMain' => false,
    ])
</template>

@push('scripts')
    <script type="module">
        let productVideoIndex = $('#product-videos-container tr').length;

        function refreshProductVideoMainRadio() {
            const radios = $('#product-videos-container input[type="radio"][name="main_video"]');

            if (radios.length === 0) {
                return;
            }

            if (radios.filter(':checked').length === 0) {
                radios.first().prop('checked', true);
            }
        }

        $('#add-product-video-btn').on('click', function () {
            const index = Date.now();
            const template = $('#product-video-row-template').html().replaceAll('__INDEX__', index);

            $('#product-videos-container').append(template);

            refreshProductVideoMainRadio();
        });

        $(document).on('click', '.btn-delete-product-video', function () {
            $(this).closest('tr').remove();

            refreshProductVideoMainRadio();
        });
    </script>
@endpush
