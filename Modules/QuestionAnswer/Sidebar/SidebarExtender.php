<?php

namespace Modules\QuestionAnswer\Sidebar;

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
                $item->item(trans('questionanswer::sidebar.questions_answers'), function (Item $item) {
                    $item->weight(40);
                    $item->setItemClass('sub-nav-item');
                    $item->route('admin.questions_answers.index');
                    $item->authorize(
                        $this->auth->hasAccess('admin.questionsanswers.index')
                    );
                });
            });
        });
    }
}
