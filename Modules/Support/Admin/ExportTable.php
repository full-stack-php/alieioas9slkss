<?php

namespace Modules\Support\Admin;

use Modules\Admin\Ui\AdminTable;

class ExportTable extends AdminTable
{

    protected array $rawColumns = ['format', 'actions', 'name'];

    public function make()
    {
        return $this->newTable()
            ->editColumn('name', function ($export) {
                $fileName = $export->file_name ? ($export->file_name . '.' . $export->format) : '';

                $html = $export->name;
                if ($fileName) {
                    $fileUrl = asset('storage/app/' . $fileName);
                    $html .= "<br /><a href='{$fileUrl}' target='_blank'><i class=\"fa fa-external-link\"></i> {$fileName}</a>";
                }

                return $html;
            })
            ->editColumn('format', function ($export) {
                return "<span class='badge badge-soft-warning rounded-pill me-1'>" . $export->format . "</span>";
            })
            ->addColumn('actions', function ($export) {
                $runUrl = route('admin.exports.run', $export->id);

                return "<button type='button' class='btn btn-sm btn-primary btn-run-export' data-url='{$runUrl}' title='Запустить экспорт'>
                            <i class='fa fa-play'></i> Запуск
                        </button>";
            });
    }
}
