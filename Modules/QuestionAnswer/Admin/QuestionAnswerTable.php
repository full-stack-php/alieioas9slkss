<?php

namespace Modules\QuestionAnswer\Admin;

use Modules\Admin\Ui\AdminTable;
use Illuminate\Http\JsonResponse;

class QuestionAnswerTable extends AdminTable
{
    /**
     * Make table response for the resource.
     *
     * @return JsonResponse
     */
    public function make()
    {
        return $this->newTable()
            ->editColumn('product', function ($questionAnswer) {
                return $questionAnswer->product->name;
            })
            ->editColumn('status', function ($questionAnswer) {
                return $questionAnswer->is_approved
                    ? '<span class="badge badge-success">' . trans('admin::admin.table.approved') . '</span>'
                    : '<span class="badge badge-warning">' . trans('admin::admin.table.pending') . '</span>';
            });
    }
}
