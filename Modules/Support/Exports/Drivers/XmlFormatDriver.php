<?php

namespace Modules\Support\Exports\Drivers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

class XmlFormatDriver
{
    protected $file;
    protected $headers = [];
    protected $settings = [];

    public function open($path, array $settings)
    {
        $this->file = fopen($path, 'w');
        $this->settings = $settings;

        // Технический заголовок оставляем всегда
        fwrite($this->file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

        // 1. Корневой открывающий тег (Root Open)
        // Если ключ есть (даже пустой), берем его. Иначе — дефолт.
        $rootOpen = array_key_exists('xml_root_open', $settings)
            ? $settings['xml_root_open']
            : '<yml_catalog date="{date}"> <shop>';

        if (!empty(trim($rootOpen))) {
            $rootOpen = str_replace('{date}', now()->format('Y-m-d H:i'), $rootOpen);
            fwrite($this->file, trim($rootOpen) . "\n");
        }

        // 2. Шапка кода (Header Code)
        if (!empty(trim($settings['xml_header_code'] ?? ''))) {
            fwrite($this->file, trim($settings['xml_header_code']) . "\n");
        }

        // 3. Обертка элементов списка (Items Wrapper)
        if (!empty(trim($settings['xml_items_wrapper'] ?? ''))) {
            fwrite($this->file, "<" . trim($settings['xml_items_wrapper']) . ">\n");
        }
    }

    public function addRow(array $row, array $settings, $item = null)
    {
        // Пропускаем техническую строку заголовков
        if (is_null($item)) {
            $this->headers = $row;
            return;
        }

        // Тег самого элемента (Item Tag)
        $itemTag = array_key_exists('xml_item_tag', $settings) ? trim($settings['xml_item_tag']) : 'product';

        if (!empty($itemTag)) {
            fwrite($this->file, "\t<{$itemTag}>\n");
        }

        // --- Умная генерация через Blade ---
        if (!empty($settings['xml_template'])) {
            try {
                $locale = $settings['locale'] ?? 'all';
                $systemLocales = function_exists('supported_locales') ? array_keys(supported_locales()) : ['ru', 'uk', 'en'];
                $originalLocale = app()->getLocale();

                if ($locale !== 'all') {
                    app()->setLocale($locale);
                }

                $renderedXml = Blade::render($settings['xml_template'], [
                    'item' => $item,
                    'locale' => $locale,
                    'locales' => $systemLocales
                ]);

                if ($locale !== 'all') {
                    app()->setLocale($originalLocale);
                }

                $formattedXml = implode("\n", array_map(function ($line) {
                    return "\t\t" . $line;
                }, explode("\n", trim($renderedXml))));

                fwrite($this->file, $formattedXml . "\n");

                if (!empty($itemTag)) {
                    fwrite($this->file, "\t</{$itemTag}>\n");
                }

                return;

            } catch (\Throwable $e) {
                Log::error("Ошибка рендера XML: " . $e->getMessage());
                if (isset($originalLocale) && isset($locale) && $locale !== 'all') {
                    app()->setLocale($originalLocale);
                }
            }
        }

        // --- Фолбэк генерация (если шаблона нет) ---
        foreach ($row as $index => $value) {
            $rawTag = $this->headers[$index] ?? "column_{$index}";
            $tag = $this->sanitizeTagName($rawTag);

            if ($value === null || $value === '') {
                continue;
            }

            if (preg_match('/[<>&]/', $value)) {
                $value = "<![CDATA[" . $value . "]]>";
            }

            fwrite($this->file, "\t\t<{$tag}>{$value}</{$tag}>\n");
        }

        if (!empty($itemTag)) {
            fwrite($this->file, "\t</{$itemTag}>\n");
        }
    }

    public function close()
    {
        // 1. Закрываем обертку элементов (Items Wrapper Close)
        $wrapper = trim($this->settings['xml_items_wrapper'] ?? '');
        if (!empty($wrapper)) {
            // Если тег был с атрибутами (например <offers class="test">), берем только первое слово
            $wrapperName = explode(' ', $wrapper)[0];
            fwrite($this->file, "</{$wrapperName}>\n");
        }

        // 2. Закрывающий корневой тег (Root Close)
        $rootClose = array_key_exists('xml_root_close', $this->settings)
            ? $this->settings['xml_root_close']
            : '</shop> </yml_catalog>';

        if (!empty(trim($rootClose))) {
            fwrite($this->file, trim($rootClose) . "\n");
        }

        fclose($this->file);
    }

    protected function sanitizeTagName(string $name): string
    {
        $name = trim($name);
        $name = str_replace(' ', '_', $name);
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
        if (preg_match('/^[0-9\-]/', $name)) {
            $name = '_' . $name;
        }

        return $name ?: 'column';
    }
}
