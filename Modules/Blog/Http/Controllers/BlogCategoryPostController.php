<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Blog\Entities\BlogPost;
use Modules\Blog\Entities\BlogCategory;
use Illuminate\Contracts\Support\Renderable;

class BlogCategoryPostController extends Controller
{
    public const NUMBER_OF_TOTAL_BLOGS_IN_BLOGS_INDEX_PAGE = 12;

    public const NUMBER_OF_RECENT_BLOGS_IN_SIDEBAR = 5;


    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index($category = null)
    {
        $categoryId = $category ?? request()->route('category');

        $blogCategory = BlogCategory::findOrFail($categoryId);

        $breadcrumbs = $this->parseBreadcrumbs($blogCategory);

        $allIds = array_merge([$blogCategory->id], $blogCategory->getDescendantsIds());

        $blogPosts = BlogPost::published()
            ->whereIn('blog_category_id', $allIds)
            ->latest()
            ->paginate(self::NUMBER_OF_TOTAL_BLOGS_IN_BLOGS_INDEX_PAGE);

        $blogCategories = BlogCategory::withCount(['blogPosts'=> fn($query) => $query->published()])->latest()->get();

        return view('storefront::public.blogs.posts.index', compact('blogCategory', 'blogPosts', 'blogCategories', 'breadcrumbs'));
    }

    private function parseBreadcrumbs($category)
    {
        $crumbs = collect();
        $current = $category;

        while ($current) {
            $crumbs->prepend($current);
            $current = $current->parent;
        }

        return $crumbs;
    }
}
