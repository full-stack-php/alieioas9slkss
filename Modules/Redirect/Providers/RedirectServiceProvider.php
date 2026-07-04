<?php

namespace Modules\Redirect\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Redirect\Admin\RedirectTabs;

class RedirectServiceProvider extends ServiceProvider
{
    public function boot()
    {
        TabManager::register('redirects', RedirectTabs::class);
    }
}
