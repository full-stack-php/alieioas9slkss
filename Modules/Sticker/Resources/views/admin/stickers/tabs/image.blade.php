<div data-sticker-types="image,info">
    @include('media::admin.image_picker.single', [
        'title' => trans('sticker::stickers.form.image'),
        'inputName' => 'files[image]',
        'file' => $sticker->image,
    ])
</div>

<div
    class="alert alert-info mb-0"
    data-sticker-types="label"
>
    {{ trans('sticker::stickers.form.label_does_not_require_image') }}
</div>
