<?php

namespace Modules\Redirect\Imports;

use Throwable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Redirect\Entities\Redirect;
use Modules\Redirect\Services\RedirectUrl;

class RedirectsImport
{
    private array $rows = [];

    private array $errors = [];

    public function handle(string $path): array
    {
        $this->rows = [];
        $this->errors = [];

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $preparedRows = $this->prepareRows($rows);

        $this->validateRows($preparedRows);

        if (!empty($this->errors)) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => count($preparedRows),
                'errors' => $this->errors,
                'has_critical_errors' => true,
            ];
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($preparedRows, &$created, &$updated) {
            foreach ($preparedRows as $row) {
                $redirect = Redirect::withoutGlobalScope('active')->firstOrNew([
                    'old_url' => $row['old_url'],
                ]);

                $exists = $redirect->exists;

                $redirect->fill([
                    'new_url' => $row['new_url'],
                    'status_code' => $row['status_code'],
                    'is_active' => $row['is_active'],
                    'comment' => $row['comment'],
                    'page_type' => RedirectUrl::detectPageType($row['old_url']),
                ]);

                $redirect->save();

                $exists ? $updated++ : $created++;
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => 0,
            'errors' => [],
            'has_critical_errors' => false,
        ];
    }

    private function prepareRows(array $rows): array
    {
        $rows = array_values(array_filter($rows, function ($row) {
            return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) > 0;
        }));

        if (empty($rows)) {
            return [];
        }

        $firstRow = array_map(fn ($value) => strtolower(trim((string) $value)), $rows[0]);

        $hasHeader = in_array('old_url', $firstRow, true) || in_array('new_url', $firstRow, true);

        if ($hasHeader) {
            return $this->prepareRowsWithHeader($rows);
        }

        return $this->prepareLegacyRows($rows);
    }

    private function prepareRowsWithHeader(array $rows): array
    {
        $header = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));

        $oldUrlIndex = array_search('old_url', $header, true);
        $newUrlIndex = array_search('new_url', $header, true);
        $statusIndex = array_search('status', $header, true);
        $statusCodeIndex = array_search('status_code', $header, true);

        if ($statusCodeIndex === false) {
            $statusCodeIndex = array_search('redirect_type', $header, true);
        }

        $commentIndex = array_search('comment', $header, true);

        $prepared = [];

        foreach ($rows as $index => $row) {
            $prepared[] = [
                'row_number' => $index + 2,
                'old_url' => RedirectUrl::normalizeOldUrl($row[$oldUrlIndex] ?? ''),
                'new_url' => RedirectUrl::normalizeNewUrl($row[$newUrlIndex] ?? ''),
                'status_code' => $this->parseStatusCode($statusCodeIndex !== false ? ($row[$statusCodeIndex] ?? null) : null),
                'is_active' => $this->parseStatus($statusIndex !== false ? ($row[$statusIndex] ?? null) : null),
                'comment' => trim((string) ($commentIndex !== false ? ($row[$commentIndex] ?? '') : '')),
            ];
        }

        return $prepared;
    }

    private function prepareLegacyRows(array $rows): array
    {
        $prepared = [];

        foreach ($rows as $index => $row) {
            $prepared[] = [
                'row_number' => $index + 1,
                'old_url' => RedirectUrl::normalizeOldUrl($row[1] ?? ''),
                'new_url' => RedirectUrl::normalizeNewUrl($row[2] ?? ''),
                'status_code' => $this->parseStatusCode($row[0] ?? null),
                'is_active' => true,
                'comment' => '',
            ];
        }

        return $prepared;
    }

    private function validateRows(array $rows): void
    {
        $oldUrls = [];

        foreach ($rows as $row) {
            $rowNumber = $row['row_number'];

            if ($row['old_url'] === '' || $row['new_url'] === '') {
                $this->errors[] = trans('redirect::redirects.import.required_error', ['row' => $rowNumber]);
                continue;
            }

            if (!RedirectUrl::isValid($row['old_url'])) {
                $this->errors[] = trans('redirect::redirects.import.invalid_old_url', ['row' => $rowNumber]);
                continue;
            }

            if (!RedirectUrl::isValid($row['new_url'])) {
                $this->errors[] = trans('redirect::redirects.import.invalid_new_url', ['row' => $rowNumber]);
                continue;
            }

            if (in_array($row['old_url'], $oldUrls, true)) {
                $this->errors[] = trans('redirect::redirects.import.duplicate_in_file', ['row' => $rowNumber]);
                continue;
            }

            $oldUrls[] = $row['old_url'];

            if (RedirectUrl::normalizeOldUrl($row['old_url']) === RedirectUrl::normalizeOldUrl($row['new_url'])) {
                $this->errors[] = trans('redirect::redirects.import.same_url', ['row' => $rowNumber]);
                continue;
            }

            $existingRedirect = Redirect::withoutGlobalScope('active')
                ->where('old_url', $row['old_url'])
                ->first();

            $exceptId = $existingRedirect?->id;

            if (Redirect::hasChain($row['new_url'], $exceptId)) {
                $this->errors[] = trans('redirect::redirects.import.chain', ['row' => $rowNumber]);
                continue;
            }

            if (Redirect::hasCycle($row['old_url'], $row['new_url'], $exceptId)) {
                $this->errors[] = trans('redirect::redirects.import.cycle', ['row' => $rowNumber]);
                continue;
            }
        }
    }

    private function parseStatus($value): bool
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return true;
        }

        return in_array($value, [
            'active',
            '1',
            'yes',
            'true',
            'on',
            'активний',
            'активный',
        ], true);
    }

    private function parseStatusCode($value): int
    {
        $value = trim((string) $value);

        if (preg_match('/\b(301|302)\b/', $value, $matches)) {
            return (int) $matches[1];
        }

        return 301;
    }
}
