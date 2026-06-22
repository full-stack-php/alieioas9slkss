<?php

namespace Modules\Blog\Services;

use Modules\Blog\Admin\PostTabs;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Blog\Http\Controllers\Admin\BlogPostController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BlogPostServiceProvider extends ServiceProvider
{

    public function boot()
    {
        TabManager::register('blog_posts', PostTabs::class);
    }

}
