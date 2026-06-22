<?php

namespace Modules\Sticker\Admin;

use Modules\Admin\Ui\AdminTable;
use Modules\Sticker\Entities\Sticker;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Exceptions\Exception;

class StickerTable extends AdminTable
{
    /**
     * Make table response for the resource.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function make()
    {
        return $this->newTable()
            ->addColumn('image', function (Sticker $sticker) {
                return view('admin::partials.table.image', [
                    'file' => $sticker->image,
                ]);
            })
            ->editColumn('type', function (Sticker $sticker) {
                return trans(
                    "sticker::stickers.form.sticker_types.{$sticker->type}"
                );
            });
    }
}
