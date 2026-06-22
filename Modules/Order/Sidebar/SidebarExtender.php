<?php

namespace Modules\Order\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    /**
     * Extend the admin sidebar menu structure.
     *
     * @param \Maatwebsite\Sidebar\Menu $menu
     * @return void
     */
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item(trans('admin::sidebar.sales'), function (Item $item) {
                $item->icon('bag-smile-bold-duotone');
                $item->toggleIcon('sidebarOrder');
                $item->weight(15);
                $item->route('admin.orders.index');
                $item->authorize(
                    $this->auth->hasAnyAccess([
                        'admin.orders.index',
                        'admin.order_statuses.index',
                        'admin.transactions.index'
                    ])
                );

                $item->item(trans('order::orders.orders'), function (Item $item) {
                    $item->weight(5);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.orders.index');
                    $item->authorize(
                        $this->auth->hasAccess('admin.orders.index')
                    );
                });

                $item->item(trans('order::statuses.statuses'), function (Item $item) {
                    $item->weight(10);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.order_statuses.index');
                    $item->authorize(
                        $this->auth->hasAccess('admin.order_statuses.index')
                    );
                });
            });
        });
    }
}
