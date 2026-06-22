<?php

namespace Modules\Support\Exports;

use Modules\Support\Exports\Contracts\ExportFormatDriver;
use Modules\Support\Exports\Drivers\CsvFormatDriver;
use Modules\Support\Exports\Drivers\XmlFormatDriver;
use InvalidArgumentException;

class ExportDriverFactory
{
    public static function make(string $format): ExportFormatDriver
    {
        return match (strtolower($format)) {
            'csv' => new CsvFormatDriver(),
            'xml' => new XmlFormatDriver(),
            // 'xlsx' => new XlsxFormatDriver(), // Добавим позже
            // 'json' => new JsonFormatDriver(), // Добавим позже
            default => throw new InvalidArgumentException("Формат {$format} не поддерживается."),
        };
    }
}
