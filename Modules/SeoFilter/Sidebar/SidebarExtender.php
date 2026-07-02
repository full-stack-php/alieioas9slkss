<?php

namespace Modules\SeoFilter\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item(trans('product::sidebar.products'), function (Item $item) {
                $item->item(trans('seo_filter::sidebar.seo_filters'), function (Item $item) {
                    $item->weight(25);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.seo_filters.index');
                    $item->isActiveWhen(route('admin.seo_filters.index', null, false));
                    $item->authorize(
                        $this->auth->hasAccess('admin.seo_filters.index')
                    );
                });
            });
        });
    }
}
