<?php

namespace Modules\Contact\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item(trans('contact::sidebar.contact_submissions'), function (Item $item) {
                $item->weight(90);
                $item->icon('phone-calling-bold-duotone');
                $item->route('admin.contact_submissions.index');
                $item->authorize(
                    $this->auth->hasAccess('admin.contact_submissions.index')
                );
            });
        });
    }
}
