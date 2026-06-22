<?php

namespace Modules\Blog\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Blog\Entities\BlogCategory;

class PostTabs extends Tabs
{

    public function make()
    {
        $this->group('post_information', trans('blog::post.tabs.group.post_information'))
            ->active()
            ->add($this->general())
            ->add($this->images())
            ->add($this->faq())
            ->add($this->seo());
    }


    private function general()
    {
        return tap(new Tab('general', trans('blog::post.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields(['name', 'h1_name', 'description', 'publish_status', 'slug']);
            $tab->view('blog::admin.posts.tabs.general', [
                'categories' => BlogCategory::all()->sortBy('name')->pluck('name', 'id')
                    ->prepend(trans('admin::admin.form.please_select'), ''),
            ]);
        });
    }

    private function images()
    {
        if (!auth()->user()->hasAccess('admin.media.index')) {
            return;
        }

        return tap(new Tab('images', trans('blog::post.tabs.images')), function (Tab $tab) {
            $tab->weight(10);
            $tab->view('blog::admin.posts.tabs.images');
        });
    }





    private function seo()
    {
        return tap(new Tab('seo', trans('blog::post.tabs.seo')), function (Tab $tab) {
            $tab->weight(15);
            $tab->view('blog::admin.posts.tabs.seo');
        });
    }
}
