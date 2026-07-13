<?php

namespace Modules\Preorder\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(
            trans('admin::sidebar.content'),
            function (Group $group) {
                $group->item(
                    trans('preorder::sidebar.preorders'),
                    function (Item $item) {
                        $item->weight(91);
                        $item->icon('phone-calling-bold-duotone');
                        $item->route('admin.preorders.index');

                        $item->authorize(
                            $this->auth->hasAccess(
                                'admin.preorders.index'
                            )
                        );
                    }
                );
            }
        );
    }
}
