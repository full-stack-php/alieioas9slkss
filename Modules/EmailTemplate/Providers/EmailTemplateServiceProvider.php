<?php

namespace Modules\EmailTemplate\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\EmailTemplate\Admin\EmailTemplateTabs;

class EmailTemplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        TabManager::register('email_templates', EmailTemplateTabs::class);
    }
}
