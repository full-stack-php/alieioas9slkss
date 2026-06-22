<?php

namespace Modules\Support\Exports\Drivers;

use Modules\Support\Exports\Contracts\ExportFormatDriver;

class CsvFormatDriver implements ExportFormatDriver
{
    protected $file;
    protected string $delimiter;
    protected string $enclosure;

    public function open(string $filePath, array $settings): void
    {
        $this->file = fopen($filePath, 'w');

        $this->delimiter = $settings['delimiter'] ?? ';';
        $this->enclosure = $settings['enclosure'] ?? '"';

        $encoding = $settings['encoding'] ?? 'utf-8';

        if (strtolower($encoding) === 'utf-8') {
            fputs($this->file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        }
    }
    public function addRow(mixed $data, array $settings, $item = null): void
    {
        $dataArray = is_array($data) ? $data : (array) $data;
        fputcsv($this->file, $dataArray, $this->delimiter, $this->enclosure);
    }

    public function close(): void
    {
        fclose($this->file);
    }
}
