<?php

namespace Modules\Redirect\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Redirect\Entities\Redirect;

class RedirectsExport
{
    public function download(array $filters = [])
    {
        $format = $filters['format'] ?? 'xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'old_url',
            'new_url',
            'redirect_type',
            'status',
            'comment',
            'page_type',
            'created_at',
            'updated_at',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNumber = 2;

        foreach ($this->query($filters)->get() as $redirect) {
            $sheet->fromArray([
                '/' . trim($redirect->old_url, '/'),
                $redirect->new_url,
                $redirect->status_code,
                $redirect->is_active ? 'active' : 'inactive',
                $redirect->comment,
                $redirect->page_type,
                optional($redirect->created_at)->format('Y-m-d H:i:s'),
                optional($redirect->updated_at)->format('Y-m-d H:i:s'),
            ], null, 'A' . $rowNumber);

            $rowNumber++;
        }

        $writerType = $format === 'csv' ? 'Csv' : 'Xlsx';
        $extension = $format === 'csv' ? 'csv' : 'xlsx';
        $contentType = $format === 'csv'
            ? 'text/csv'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        $writer = IOFactory::createWriter($spreadsheet, $writerType);

        if ($writerType === 'Csv') {
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'redirects.' . $extension, [
            'Content-Type' => $contentType,
        ]);
    }

    private function query(array $filters)
    {
        $query = Redirect::withoutGlobalScope('active');

        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        if (!empty($filters['status_code'])) {
            $query->where('status_code', (int) $filters['status_code']);
        }

        if (!empty($filters['page_type'])) {
            $query->where('page_type', $filters['page_type']);
        }

        return $query->orderBy('id');
    }
}
