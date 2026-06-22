<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Services\BlogCategoryTreeUpdater;
use Modules\Blog\Http\Responses\BlogCategoryTreeResponse;

class BlogCategoryTreeController
{
    /**
     * Display category tree in json.
     *
     * @return Response
     */
    public function index()
    {
        $categories = BlogCategory::withoutGlobalScope('active')
            ->orderByRaw('-position DESC')
            ->get();

        return new BlogCategoryTreeResponse($categories);
    }


    /**
     * Update category tree in storage.
     *
     * @return Response
     */
    public function update()
    {
        BlogCategoryTreeUpdater::update(request('category_tree'));

        return trans('category::messages.category_order_updated');
    }
}
