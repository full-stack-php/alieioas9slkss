<?php

namespace Modules\Sticker\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    /**
     * Extend the admin sidebar.
     *
     * @param Menu $menu
     *
     * @return void
     */
    public function extend(Menu $menu)
    {
        $menu->group(
            trans('admin::sidebar.content'),
            function (Group $group) {
                $group->item(
                    trans('product::sidebar.products'),
                    function (Item $item) {
                        $item->item(
                            trans('sticker::sidebar.stickers'),
                            function (Item $item) {
                                $item->weight(35);
                                $item->setItemClass('sub-nav-item');
                                $item->route('admin.stickers.index');

                                $item->authorize(
                                    $this->auth->hasAccess(
                                        'admin.stickers.index'
                                    )
                                );
                            }
                        );
                    }
                );
            }
        );
    }
}
