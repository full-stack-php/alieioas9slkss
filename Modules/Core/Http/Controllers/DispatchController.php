<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class DispatchController extends Controller
{
    public function handle(Request $request)
    {
        $resolved = $request->attributes->get('resolved_entity');

        if (!$resolved) {
            abort(404);
        }
        $parameter = $resolved['slug'] ?? $resolved['id'];

        if ($resolved['type'] === 'blog_post') {
            $post = \Modules\Blog\Entities\BlogPost::with('category')->find($resolved['id']);

            return app()->call([app($resolved['controller']), $resolved['method']], [
                'category' => $post->category->slug,
                'slug'     => $post->slug,
            ]);
        }

        if ($resolved['type'] === 'blog_category') {
            return app()->call([app($resolved['controller']), $resolved['method']], [
                'category' => $resolved['id'],
            ]);
        }

        if ($resolved['type'] === 'product') {
            return app()->call([app($resolved['controller']), $resolved['method']], [
                'slug' => $resolved['slug'],
            ]);
        }


        return app()->call([app($resolved['controller']), $resolved['method']], [
            'slug' => $parameter,
            'category' => $parameter,
        ]);
    }
}
