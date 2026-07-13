<?php

namespace Modules\Storefront\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Menu\MegaMenu\MegaMenu;

class MobileMenuController
{
    /**
     * Display mobile menu html.
     *
     * @return Response
     */
    public function index()
    {
        $menu = new MegaMenu(setting('storefront_mobile_menu'));

        $items = $this->normalizeItems($menu->menus());

        return response()->view('storefront::public.layouts.mobile_menu.items', compact('items'));
    }


    private function normalizeItems($items): array
    {
        return collect($items)
            ->map(function ($item) {
                $children = $this->childrenOf($item);

                return [
                    'name' => $item->name(),
                    'url' => $item->url(),
                    'target' => $item->target(),
                    'icon' => $this->iconOf($item),
                    'children' => $this->normalizeItems($children),
                ];
            })
            ->values()
            ->all();
    }


    private function childrenOf($item)
    {
        if (method_exists($item, 'hasSubMenus') && $item->hasSubMenus()) {
            return $item->subMenus();
        }

        if (method_exists($item, 'hasItems') && $item->hasItems()) {
            return $item->items();
        }

        return collect();
    }


    private function iconOf($item): ?string
    {
        if (method_exists($item, 'hasIcon') && $item->hasIcon()) {
            return $item->icon();
        }

        return null;
    }
}
