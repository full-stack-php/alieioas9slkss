<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Blog\Entities\BlogPost;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Entities\BlogPostTranslation;

class BlogPostController extends Controller
{
    public const NUMBER_OF_RECENT_BLOGS_IN_SIDEBAR = 5;

    public const NUMBER_OF_TOTAL_BLOGS_IN_BLOGS_INDEX_PAGE = 10;


    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        $blogPosts = BlogPost::published()
            ->latest()
            ->paginate(self::NUMBER_OF_TOTAL_BLOGS_IN_BLOGS_INDEX_PAGE);

        $recentBlogPosts = BlogPost::published()
            ->latest()
            ->take(self::NUMBER_OF_RECENT_BLOGS_IN_SIDEBAR)->get();

        $blogCategories = BlogCategory::withCount(['blogPosts'=> fn($query) => $query->published()])->latest()->get();


        return view('storefront::public.blogs.posts.index', compact( 'blogPosts', 'recentBlogPosts', 'blogCategories'));
    }


    /**
     * Show the specified resource.
     *
     * @param $slug
     *
     * @return Renderable
     */
    public function show($category, $slug)
    {
        $blogPost = BlogPost::findBySlug($slug)->whereHas('category', function($q) use ($category) {
            $q->where('slug', $category);
        })->published()->firstOrFail();

        if (!$blogPost) {
            abort(404);
        }
        $blogCategory = $blogPost->category;
        $breadcrumbs = $this->parseBreadcrumbs($blogCategory);


        return view('storefront::public.blogs.posts.show', compact('blogPost', 'breadcrumbs'));
    }


    /**
     * Show the specified resource.
     *
     * @param Request $request
     *
     * @return Renderable
     */
    public function search(Request $request)
    {
        $blogCategories = BlogCategory::withCount(['blogPosts'=> fn($query) => $query->published()])->latest()->get();

        $blogPosts = BlogPost::published()->whereHas('translations', function($query) use ($request) {
            $query->where('title', 'LIKE', '%' . $request->input('query') . '%');
        })->paginate(self::NUMBER_OF_TOTAL_BLOGS_IN_BLOGS_INDEX_PAGE);;

        $recentBlogPosts = BlogPost::published()
            ->latest()
            ->take(self::NUMBER_OF_RECENT_BLOGS_IN_SIDEBAR)
            ->get();

        $indexTitle = trans('storefront::blog.search_results_for') . ' ' . $request->input('query');

        return view('storefront::public.blogs.posts.index', compact('indexTitle', 'blogCategories', 'blogPosts', 'recentBlogPosts'));
    }

    private function parseBreadcrumbs($model)
    {
        $crumbs = collect();

        if ($model instanceof BlogPost) {
            $crumbs->push($model);
            $current = $model->blogCategory;
        } else {
            $current = $model;
        }

        while ($current) {
            $crumbs->prepend($current);
            $current = $current->parent;
        }

        return $crumbs;
    }
}
