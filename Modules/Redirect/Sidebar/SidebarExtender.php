<?php

namespace Modules\Redirect\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.system'), function (Group $group) {
            $group->item(trans('redirect::sidebar.redirects'), function (Item $item) {
                $item->icon('routing-2-bold-duotone');
                $item->weight(25);
                $item->route('admin.redirects.index');
                $item->authorize(
                    $this->auth->hasAccess('admin.redirects.index')
                );
            });
        });
    }
}
