<?php

namespace Modules\Redirect\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Modules\Redirect\Entities\Redirect;

class RedirectsExport
{
    public function download(array $filters = [])
    {
        $format = $filters['format'] ?? 'xlsx';

        if (!in_array($format, ['xlsx', 'csv'], true)) {
            $format = 'xlsx';
        }

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
                $this->cell('/' . trim((string) $redirect->old_url, '/')),
                $this->cell($redirect->new_url),
                $this->cell($redirect->status_code),
                $this->cell($redirect->is_active ? 'active' : 'inactive'),
                $this->cell($redirect->comment),
                $this->cell($redirect->page_type),
                $this->cell(optional($redirect->created_at)->format('Y-m-d H:i:s')),
                $this->cell(optional($redirect->updated_at)->format('Y-m-d H:i:s')),
            ], null, 'A' . $rowNumber);

            $rowNumber++;
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writerType = $format === 'csv' ? 'Csv' : 'Xlsx';
        $extension = $format === 'csv' ? 'csv' : 'xlsx';

        $writer = IOFactory::createWriter($spreadsheet, $writerType);

        if ($writerType === 'Csv') {
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setUseBOM(true);
        }

        $directory = storage_path('app/redirects_exports');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $downloadName = 'redirects-' . now()->format('Y-m-d-H-i-s') . '.' . $extension;
        $filePath = tempnam($directory, 'redirects_');

        $writer->save($filePath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $this->cleanOutputBuffers();

        return response()
            ->download($filePath, $downloadName, [
                'Content-Type' => $this->contentType($format),
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                'Pragma' => 'public',
            ])
            ->deleteFileAfterSend(true);
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

    private function cell($value): string
    {
        $value = (string) ($value ?? '');

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value) ?? '';
    }

    private function contentType($format): string
    {
        return $format === 'csv'
            ? 'text/csv; charset=UTF-8'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    private function cleanOutputBuffers(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}
