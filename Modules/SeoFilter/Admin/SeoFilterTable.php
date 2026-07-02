<?php

namespace Modules\SeoFilter\Admin;

use Illuminate\Http\JsonResponse;
use Modules\Admin\Ui\AdminTable;
use Modules\SeoFilter\Entities\SeoFilter;
use Yajra\DataTables\Exceptions\Exception;

class SeoFilterTable extends AdminTable
{
    protected array $rawColumns = ['path'];

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function make()
    {
        return $this->newTable()
            ->addColumn('category', function (SeoFilter $seoFilter) {
                return $seoFilter->category->exists ? $seoFilter->category->name : '—';
            })
            ->editColumn('path', function (SeoFilter $seoFilter) {
                return '<a href="' . e($seoFilter->url()) . '" target="_blank">' . e($seoFilter->path) . '</a>';
            })
            ->editColumn('status', function (SeoFilter $seoFilter) {
                return $seoFilter->status
                    ? '<span class="badge badge-soft-success rounded-pill me-1">' . trans('admin::admin.table.active') . '</span>'
                    : '<span class="badge badge-soft-danger rounded-pill me-1">' . trans('admin::admin.table.inactive') . '</span>';
            });
    }
}
