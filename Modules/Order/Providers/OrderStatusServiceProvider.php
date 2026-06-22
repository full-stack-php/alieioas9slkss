<?php

namespace Modules\Order\Providers;

use Modules\Order\Admin\OrderStatusTabs;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;

class OrderStatusServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        TabManager::register('order_statuses', OrderStatusTabs::class);
    }
}
