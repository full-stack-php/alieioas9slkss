<?php

namespace Modules\Sticker\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Sticker\Admin\ProductTabsExtender;
use Modules\Sticker\Admin\StickerTabs;

class StickerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot()
    {
        TabManager::register('stickers', StickerTabs::class);

        TabManager::extend(
            'products',
            ProductTabsExtender::class
        );
    }
}
