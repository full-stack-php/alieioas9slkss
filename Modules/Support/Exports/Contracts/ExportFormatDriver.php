<?php

namespace Modules\Support\Exports\Contracts;

interface ExportFormatDriver
{
    public function open(string $filePath, array $settings): void;

    public function addRow(mixed $data, array $settings): void;

    public function close(): void;
}
