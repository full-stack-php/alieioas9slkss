<?php

namespace Modules\EmailTemplate\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;

class EmailTemplateTabs extends Tabs
{
    public function make()
    {
        $this->group('email_template_information', trans('emailtemplate::email_templates.tabs.group.email_template_information'))
            ->active()
            ->add($this->general())
            ->add($this->content());
    }

    private function general()
    {
        return tap(new Tab('general', trans('emailtemplate::email_templates.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields([
                'type',
                'recipient',
                'status_key',
                'is_active',
                'show_product_image',
                'product_image_max_width',
                'product_image_max_height',
                'sort_order',
            ]);
            $tab->view('emailtemplate::admin.email_templates.tabs.general');
        });
    }

    private function content()
    {
        return tap(new Tab('content', trans('emailtemplate::email_templates.tabs.content')), function (Tab $tab) {
            $tab->weight(10);
            $tab->fields([
                'name',
                'subject',
                'header',
                'body',
                'footer',
            ]);
            $tab->view('emailtemplate::admin.email_templates.tabs.content');
        });
    }
}
