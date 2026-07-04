<?php

namespace Modules\EmailTemplate\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.system'), function (Group $group) {
            $group->item(trans('emailtemplate::sidebar.email_templates'), function (Item $item) {
                $item->icon('letter-bold-duotone');
                $item->weight(26);
                $item->route('admin.email_templates.index');
                $item->authorize(
                    $this->auth->hasAccess('admin.email_templates.index')
                );
            });
        });
    }
}
