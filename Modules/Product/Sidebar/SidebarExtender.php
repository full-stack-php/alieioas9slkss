<?php

namespace Modules\Product\Sidebar;

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
                $item->toggleIcon('sidebarCatalog');
                $item->icon('t-shirt-bold-duotone');
                $item->weight(10);
                $item->route('admin.products.index');
                $item->authorize(
                    $this->auth->hasAnyAccess([
                        'admin.products.create',
                        'admin.products.index',
                        'admin.categories.index',
                        'admin.product_one_c_mappings.index',
                        'admin.attributes.index',
                        'admin.attribute_sets.index',
                        'admin.options.index',
                        'admin.seo_filters.index',
                    ])
                );

                $item->item(trans('product::sidebar.all_products'), function (Item $item) {
                    $item->weight(6);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.products.index');
                    $item->isActiveWhen(route('admin.products.index', null, false));
                    $item->authorize(
                        $this->auth->hasAccess('admin.products.index')
                    );
                });
                $item->item(trans('product::sidebar.one_c_mappings'), function (Item $item) {
                    $item->weight(7);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.product_one_c_mappings.index');
                    $item->isActiveWhen(route('admin.product_one_c_mappings.index', null, false));
                    $item->authorize(
                        $this->auth->hasAccess('admin.product_one_c_mappings.index')
                    );
                });
            });
        });
    }
}
