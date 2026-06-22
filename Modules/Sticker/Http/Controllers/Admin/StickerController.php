<?php

namespace Modules\Sticker\Http\Controllers\Admin;

use Modules\Sticker\Entities\Sticker;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Sticker\Http\Requests\SaveStickerRequest;

class StickerController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = Sticker::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'sticker::stickers.sticker';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'sticker::admin.stickers';

    /**
     * Form request for the resource.
     *
     * @var string
     */
    protected $validation = SaveStickerRequest::class;
}
