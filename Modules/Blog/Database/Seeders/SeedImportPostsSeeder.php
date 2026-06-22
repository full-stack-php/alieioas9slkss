<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Entities\BlogPost;

class SeedImportPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->warn('User dump:');
        $path = base_path('Modules/Blog/Database/Seeders/data.csv');

        $handle = fopen($path, 'r');
        $delimiter = "\t";
        $enclosure = '"';

        $buffer = '';
        $rowIndex = 0;

        while (($line = fgets($handle)) !== false) {
            $buffer .= $line;

            // посчитаем количество enclosure в buffer — если нечётное, значит ещё не закрылась строка
            $countEnclosures = substr_count($buffer, $enclosure);

            // Если кавычек чётное число — можно парсить
            if ($countEnclosures % 2 === 0) {
                // распарсим строку (учитываем многострочные поля)
                $row = str_getcsv($buffer, $delimiter, $enclosure, '\\');

                // очистка полей
                $row = array_map(function ($cell) {
                    if ($cell === null) return $cell;
                    // удаляем BOM + лишние пробелы по краям
                    $cell = preg_replace('/^\x{FEFF}/u', '', $cell);
                    return trim($cell);
                }, $row);



                $data = explode('|', json_encode($row, JSON_UNESCAPED_UNICODE));

                $modelData  = [
                    'slug' => $data[8],
                    'blog_category_id' => 4,
                    'publish_status' => "published",
                    'ru' => [
                        'name' => $data[1],
                        'h1_name' => $data[1],
                        'description' => $data[6],
                    ],
                    'uk' => [
                        'name' => $data[2],
                        'h1_name' => $data[2],
                        'description' => $data[7],
                    ],
                ];
                $meta = [
                    'uk' => [
                        'meta_title' => $data[10],
                        'meta_description' => $data[11],
                    ],
                    'ru' => [
                        'meta_title' => $data[12],
                        'meta_description' => $data[13],
                    ]
                ];

                $postId = BlogPost::create($modelData);

                $postId->saveMetaData($meta);

                $this->command->line("Row {$rowIndex}: " . $data[1]);
                $rowIndex++;
                $buffer = '';
            } else {
                // продолжаем накапливать — поле многострочное
                continue;
            }
        }

        fclose($handle);
    }
}
