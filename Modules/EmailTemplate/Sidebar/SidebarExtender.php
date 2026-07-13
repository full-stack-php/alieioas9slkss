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
            $group->item(trans('admin::sidebar.tools'), function (Item $item) {
                $item->item(trans('emailtemplate::sidebar.email_templates'), function (Item $item) {
                    $item->weight(26);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.email_templates.index');
                    $item->authorize(
                        $this->auth->hasAccess('admin.email_templates.index')
                    );
                });
            });
        });
    }
}
