<?php

namespace Modules\Support\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Support\Admin\ExportTabs;

class ExportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TabManager::register('export_profiles', ExportTabs::class);
    }

}
