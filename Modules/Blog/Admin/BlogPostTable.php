<?php

namespace Modules\Blog\Admin;

use Modules\Admin\Ui\AdminTable;
use Modules\Blog\Entities\BlogPost;
use Yajra\DataTables\Exceptions\Exception;

class BlogPostTable extends AdminTable
{


    /**
     * Make table response for the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function make()
    {

        return $this->newTable()
            ->addColumn('preview', function (BlogPost $blogPost) {

                return view('admin::partials.table.image', [
                    'file' => $blogPost->preview,
                ]);
            })
            ->addColumn('name', function ($blogPost) {
                return $blogPost->name;
            });
//            ->addColumn('user', function ($blogPost) {
//                return $blogPost->user->full_name;
//            })
//            ->addColumn('publish_status', function ($blogPost) {
//                return match ($blogPost->publish_status) {
//                    BlogPost::PUBLISHED => '<span class="badge badge-success">' . trans('blog::blog.posts.form.publish_status.published') . '</span>',
//                    BlogPost::UNPUBLISHED => '<span class="badge badge-danger">' . trans("blog::blog.posts.form.publish_status.unpublished") . '</span>',
//                };
//            });
    }
}
