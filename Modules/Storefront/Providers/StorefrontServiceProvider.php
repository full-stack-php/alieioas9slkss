<?php

namespace Modules\Storefront\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Storefront\Admin\StorefrontTabs;
use Modules\Storefront\Http\ViewComposers\LayoutComposer;
use Modules\Storefront\Http\ViewComposers\HomePageComposer;
use Modules\Storefront\Http\ViewComposers\AuthLayoutComposer;
use Modules\Storefront\Http\ViewComposers\StorefrontTabsComposer;
use Modules\Storefront\Http\ViewComposers\ProductShowPageComposer;
use Modules\Storefront\Http\ViewComposers\ProductIndexPageComposer;
use Modules\Storefront\Http\ViewComposers\ProductFilterComposer;

class StorefrontServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TabManager::register('storefront', StorefrontTabs::class);

        View::composer([
            'storefront::public.products.index',
            'storefront::public.categories.show',
            'storefront::public.brands.show',
        ], ProductFilterComposer::class);

        View::composer('storefront::public.layout', LayoutComposer::class);
        View::composer('storefront::public.auth.*', AuthLayoutComposer::class);
        View::composer('storefront::public.home.index', HomePageComposer::class);
        View::composer('storefront::public.products.index', ProductIndexPageComposer::class);
        View::composer('storefront::public.products.show', ProductShowPageComposer::class);
        View::composer('storefront::admin.storefront.tabs.*', StorefrontTabsComposer::class);

        Paginator::defaultView('storefront::public.pagination');
    }
}
