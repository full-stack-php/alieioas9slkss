<?php

namespace Modules\Admin\Sidebar;

use Maatwebsite\Sidebar\Presentation\Illuminate\IlluminateSidebarRenderer;
use Maatwebsite\Sidebar\Sidebar;

class AdminSidebarRenderer extends IlluminateSidebarRenderer
{
    protected $view = 'admin::partials.sidebar_parts.menu';


    public function render(Sidebar $sidebar)
    {
        $menu = $sidebar->getMenu();

        if ($menu->isAuthorized()) {
            $groups = [];
            foreach ($menu->getGroups() as $group) {
                $groups[] = (new AdminGroupRenderer($this->factory))->render($group);
            }

            return $this->factory->make($this->view, [
                'groups' => $groups
            ]);
        }
    }
}
