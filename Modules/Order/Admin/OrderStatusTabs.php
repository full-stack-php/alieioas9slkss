<?php

namespace Modules\Order\Admin;

use Modules\Admin\Ui\Tabs;
use Modules\Admin\Ui\Tab;

class OrderStatusTabs extends Tabs
{
    /**
     * Make tabs architecture.
     *
     * @return void
     */
    public function make()
    {
        $this->group('order_status_information', trans('order::statuses.tabs.group.order_status_info'))
            ->active()
            ->add($this->general());
    }

    /**
     * Build the general properties tab layout.
     *
     * @return Tab
     */
    private function general()
    {
        return tap(new Tab('general', trans('order::statuses.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->view('order::admin.order_statuses.tabs.general');
        });
    }
}
