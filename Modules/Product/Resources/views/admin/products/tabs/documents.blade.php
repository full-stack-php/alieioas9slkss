@include('media::admin.file_picker.multiple', [
    'title' => trans('product::products.form.documents.title'),
    'description' => trans('product::products.form.documents.description'),
    'inputName' => 'downloads[]',
    'files' => $product->downloads,
])
