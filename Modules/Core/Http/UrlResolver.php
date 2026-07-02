<?php

namespace Modules\Core\Http;

use Illuminate\Support\Facades\Cache;
use Modules\Blog\Entities\BlogPost;
use Modules\Blog\Http\Controllers\BlogCategoryPostController;
use Modules\Blog\Http\Controllers\BlogPostController;
use Modules\Category\Http\Controllers\CategoryProductController;
use Modules\Page\Entities\Page;
use Modules\Category\Entities\Category;
use Modules\Blog\Entities\BlogCategory;
use Modules\Page\Http\Controllers\PageController;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Controllers\ProductController;
use Modules\SeoFilter\Entities\SeoFilter;
use Modules\SeoFilter\Http\Controllers\SeoFilterController;

class UrlResolver
{
    /**
     * Получить данные сущности по текущему пути.
     */
    public function resolve($path)
    {
        $path = trim($path, '/');

        $map = Cache::rememberForever('app_url_map', function () {
            return $this->buildMap();
        });

        return $map[$path] ?? null;
    }

    /**
     * Сборка карты всех URL системы.
     */
    private function buildMap()
    {
        $map = [];

        Page::all()->each(function ($page) use (&$map) {
            $map[$page->slug] = [
                'type' => 'page',
                'slug' => $page->slug,
                'controller' => PageController::class,
                'method' => 'show'
            ];
        });

        Category::with('parent')->get()->each(function ($category) use (&$map) {
            $path = $category->getFullPath();
            $map[$path] = [
                'type' => 'category',
                'slug' => $category->slug,
                'controller' => CategoryProductController::class,
                'method' => 'index'
            ];
        });

        BlogCategory::with('parent')->get()->each(function ($blogCat) use (&$map) {
            $path = $blogCat->getFullPath();

            $map[$path] = [
                'type' => 'blog_category',
                'id' => $blogCat->id,
                'slug' => $blogCat->slug,
                'controller' => BlogCategoryPostController::class,
                'method' => 'index'
            ];
        });

        BlogPost::with('category')->get()->each(function ($post) use (&$map) {
            if ($post->category) {
                $path = $post->category->getFullPath() . '/' . $post->slug;
            } else {
                $path = $post->slug;
            }

            $map[$path] = [
                'type' => 'blog_post',
                'id' => $post->id,
                'slug' => $post->slug,
                'controller' => BlogPostController::class,
                'method' => 'show'
            ];
        });

        Product::all()->each(function ($product) use (&$map) {
            $path = 'product/' . $product->slug;

            $map[$path] = [
                'type' => 'product',
                'slug' => $product->slug,
                'id'   => $product->id,
                'controller' => ProductController::class,
                'method' => 'show'
            ];
        });

        SeoFilter::active()
            ->with('category')
            ->get()
            ->each(function ($seoFilter) use (&$map) {
                $path = trim($seoFilter->fullPath(), '/');

                if ($path === '') {
                    return;
                }

                if (isset($map[$path])) {
                    return;
                }

                $map[$path] = [
                    'type' => 'seo_filter',
                    'id' => $seoFilter->id,
                    'slug' => $path,
                    'controller' => SeoFilterController::class,
                    'method' => 'show',
                ];
            });

        return $map;
    }

    private function generateRecursivePath($model)
    {
        $slugs = [$model->slug];
        $parent = $model->parent;

        while ($parent) {
            array_unshift($slugs, $parent->slug);
            $parent = $parent->parent;
        }

        return implode('/', $slugs);
    }
}
