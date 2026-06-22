<?php

namespace Modules\Option\Providers;

use Modules\Option\Admin\OptionTabs;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Option\Admin\ProductTabsExtender;

class OptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        TabManager::register('options', OptionTabs::class);
        TabManager::extend('products', ProductTabsExtender::class);
    }
}
