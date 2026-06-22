<?php

namespace Modules\Sticker\Providers;

use Modules\Product\Entities\Product;
use Modules\Sticker\Listeners\SaveProductStickers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Product::saved(SaveProductStickers::class);
    }
}
