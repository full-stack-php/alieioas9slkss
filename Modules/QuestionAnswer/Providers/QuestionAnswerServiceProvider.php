<?php

namespace Modules\QuestionAnswer\Providers;

use Modules\QuestionAnswer\Admin\QuestionAnswerTabs;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;

class QuestionAnswerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        TabManager::register('questions_answers', QuestionAnswerTabs::class);
    }
}
