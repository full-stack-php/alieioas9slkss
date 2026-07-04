<?php

namespace Modules\Redirect\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;

class RedirectTabs extends Tabs
{
    public function make()
    {
        $this->group('redirect_information', trans('redirect::redirects.tabs.group.redirect_information'))
            ->active()
            ->add($this->general());
    }

    private function general()
    {
        return tap(new Tab('general', trans('redirect::redirects.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields([
                'old_url',
                'new_url',
                'status_code',
                'is_active',
            ]);
            $tab->view('redirect::admin.redirects.tabs.general');
        });
    }
}
