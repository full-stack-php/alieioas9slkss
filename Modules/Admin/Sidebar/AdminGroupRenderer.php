<?php

namespace Modules\Admin\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Presentation\Illuminate\IlluminateGroupRenderer;

class AdminGroupRenderer extends IlluminateGroupRenderer
{
    protected $view = 'admin::partials.sidebar_parts.group';

    public function render(Group $group)
    {
        if ($group->isAuthorized()) {
            $items = [];
            foreach ($group->getItems() as $item) {
                $items[] = (new AdminItemRenderer($this->factory))->render($item);
            }

            return $this->factory->make($this->view, [
                'group' => $group,
                'items' => $items
            ])->render();
        }
    }

}
