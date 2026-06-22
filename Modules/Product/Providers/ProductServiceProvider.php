<?php

namespace Modules\Product\Providers;

use Modules\Admin\Ui\Facades\TabManager;
use Modules\Product\Admin\ProductTabs;
use Modules\Product\RecentlyViewed;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Product\Http\ViewComposers\ProductEditPageComposer;
use Modules\Product\Http\ViewComposers\ProductCreatePageComposer;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        TabManager::register('products', ProductTabs::class);

    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(RecentlyViewed::class, function ($app) {
            return new RecentlyViewed(
                $app['session'],
                $app['events'],
                'recently_viewed',
                session()->getId() . '_recently_viewed',
                config('korf.modules.product.config.recently_viewed')
            );
        });

        $this->app->alias(RecentlyViewed::class, 'recently_viewed');
    }
}
