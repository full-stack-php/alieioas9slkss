<?php

namespace Modules\Admin\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Presentation\ActiveStateChecker;
use Maatwebsite\Sidebar\Presentation\Illuminate\IlluminateAppendRenderer;
use Maatwebsite\Sidebar\Presentation\Illuminate\IlluminateBadgeRenderer;
use Maatwebsite\Sidebar\Presentation\Illuminate\IlluminateItemRenderer;

class AdminItemRenderer extends IlluminateItemRenderer
{
    protected $view = 'admin::partials.sidebar_parts.item';

    public function render(Item $item)
    {
        if ($item->isAuthorized()) {
            $items = [];
            foreach ($item->getItems() as $child) {
                $child->isChild = true;
                $items[] = (new AdminItemRenderer($this->factory))->render($child);
            }

            $badges = [];
            foreach ($item->getBadges() as $badge) {
                $badges[] = (new IlluminateBadgeRenderer($this->factory))->render($badge);
            }

            $appends = [];
            foreach ($item->getAppends() as $append) {
                $appends[] = (new IlluminateAppendRenderer($this->factory))->render($append);
            }

            return $this->factory->make($this->view, [
                'item'    => $item,
                'items'   => $items,
                'badges'  => $badges,
                'appends' => $appends,
                'active'  => (new ActiveStateChecker())->isActive($item),
            ])->render();
        }
    }
}
