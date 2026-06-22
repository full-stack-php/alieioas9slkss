<input
    type="hidden"
    name="_sync_stickers"
    value="1"
>

<div class="row">
    <div class="col-md-12">
        {{ Form::select(
            'stickers',
            trans('sticker::attributes.stickers'),
            $errors,
            $stickerOptions,
            [
                'stickers' => $selectedStickerIds->all(),
            ],
            [
                'multiple' => true,
                'data-choices-search-true' => true,
                'data-choices-sorting-false' => true,
                'help' => trans(
                    'sticker::stickers.form.product_stickers_help'
                ),
            ]
        ) }}
    </div>
</div>
