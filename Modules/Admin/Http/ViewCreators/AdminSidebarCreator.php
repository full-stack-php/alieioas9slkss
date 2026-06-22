<?php

namespace Modules\Admin\Http\ViewCreators;

use Illuminate\View\View;
use Modules\Admin\Sidebar\AdminSidebar;
use Maatwebsite\Sidebar\Presentation\SidebarRenderer;
use Modules\Admin\Sidebar\AdminSidebarRenderer;

class AdminSidebarCreator
{
    /**
     * @var AdminSidebar
     */
    protected $sidebar;

    /**
     * @var AdminSidebarRenderer
     */
    protected $renderer;


    /**
     * @param AdminSidebar $sidebar
     * @param AdminSidebarRenderer $renderer
     */
    public function __construct(AdminSidebar $sidebar, AdminSidebarRenderer $renderer)
    {
        $this->sidebar = $sidebar;
        $this->renderer = $renderer;
    }


    public function create(View $view)
    {
        $view->sidebar = $this->renderer->render($this->sidebar);
    }
}
