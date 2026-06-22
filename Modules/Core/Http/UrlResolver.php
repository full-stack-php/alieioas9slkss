<?php

namespace Modules\Core\Http;

use Illuminate\Support\Facades\Cache;
use Modules\Blog\Entities\BlogPost;
use Modules\Page\Entities\Page;
use Modules\Category\Entities\Category;
use Modules\Blog\Entities\BlogCategory;

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
                'controller' => \Modules\Page\Http\Controllers\PageController::class,
                'method' => 'show'
            ];
        });

        Category::with('parent')->get()->each(function ($category) use (&$map) {
            $path = $category->getFullPath();
            $map[$path] = [
                'type' => 'category',
                'slug' => $category->slug,
                'controller' => \Modules\Category\Http\Controllers\CategoryProductController::class,
                'method' => 'index'
            ];
        });

        BlogCategory::with('parent')->get()->each(function ($blogCat) use (&$map) {
            $path = $blogCat->getFullPath();

            $map[$path] = [
                'type' => 'blog_category',
                'id' => $blogCat->id,
                'slug' => $blogCat->slug,
                'controller' => \Modules\Blog\Http\Controllers\BlogCategoryPostController::class,
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
                'controller' => \Modules\Blog\Http\Controllers\BlogPostController::class,
                'method' => 'show'
            ];
        });

        \Modules\Product\Entities\Product::all()->each(function ($product) use (&$map) {
            $path = 'product/' . $product->slug;

            $map[$path] = [
                'type' => 'product',
                'slug' => $product->slug,
                'id'   => $product->id,
                'controller' => \Modules\Product\Http\Controllers\ProductController::class,
                'method' => 'show'
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
