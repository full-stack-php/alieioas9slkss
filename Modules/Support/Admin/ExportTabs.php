<?php

namespace Modules\Support\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;

class ExportTabs extends Tabs
{
    public function make()
    {
        $this->group('export_information', trans('support::export.tabs.group.export_information'))
            ->active()
            ->add($this->general())
            ->add($this->settings())
            ->add($this->columns())
            ->add($this->filters());
    }

    private function general()
    {
        $systemLocales = collect(supported_locales())->map(function ($locale) {
            return $locale['name'];
        })->toArray();

        // Добавляем пункт "Все языки" в самое начало
        $locales = ['all' => 'Все языки'] + $systemLocales;

        return tap(new Tab('general', trans('support::export.tabs.general')), function (Tab $tab) use ($locales) {
            $tab->active();
            $tab->weight(5);
            $tab->fields(['name', 'entity', 'format', 'is_active', 'cron_schedule']);
            $tab->view('support::admin.exports.tabs.general', [
                'entities' => [
                    'Modules\Product\Entities\Product' => 'Товары',
                    'Modules\Category\Entities\Category' => 'Категории',
                    'Modules\Brand\Entities\Brand' => 'Производители',
                    'Modules\Blog\Entities\Category' => 'Категории блог',
                    'Modules\Blog\Entities\BlogPost' => 'Посты блога',
                ],
                'formats' => [
                    'csv' => 'CSV',
                    'xlsx' => 'XLSX',
                    'xml' => 'XML',
                    'json' => 'JSON',
                ],
                'locales' => $locales
            ]);
        });
    }

    private function settings()
    {
        return tap(new Tab('settings', trans('support::export.tabs.settings')), function (Tab $tab) {
            $tab->weight(10);
            $tab->fields(['settings']);
            $tab->view('support::admin.exports.tabs.settings');
        });
    }

    private function columns()
    {
        return tap(new Tab('columns', trans('support::export.tabs.columns')), function (Tab $tab) {
            $tab->weight(15);
            $tab->fields(['columns']);
            $tab->view('support::admin.exports.tabs.columns');
        });
    }

    private function filters()
    {
        return tap(new Tab('filters', trans('support::export.tabs.filters')), function (Tab $tab) {
            $tab->weight(20);
            $tab->fields(['filters']);
            $tab->view('support::admin.exports.tabs.filters');
        });
    }
}
