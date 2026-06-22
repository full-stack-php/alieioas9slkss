@include('media::admin.image_picker.single', [
    'title' => trans('blog::attributes.posts.preview'),
    'inputName' => 'files[preview]',
    'file' => $blogPost->preview,
])

<div class="media-picker-divider"></div>

@include('media::admin.image_picker.single', [
    'title' => trans('blog::attributes.posts.full_image'),
    'inputName' => 'files[full_image]',
    'file' => $blogPost->full_image,
])
