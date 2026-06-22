<?php

namespace Modules\Coupon\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item(trans('coupon::coupons.coupons'), function (Item $item) {
                $item->icon('confetti-minimalistic-bold-duotone');
                $item->weight(20);
                $item->route('admin.coupons.index');
                $item->authorize(
                    $this->auth->hasAccess('admin.coupons.index')
                );
            });
        });
    }
}
