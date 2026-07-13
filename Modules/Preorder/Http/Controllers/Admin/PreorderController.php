<?php

namespace Modules\Preorder\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Preorder\Entities\Preorder;

class PreorderController
{
    public function index()
    {
        return view('preorder::admin.preorders.index');
    }

    public function table(Request $request)
    {
        return (new Preorder())->table($request);
    }

    public function destroy(string $ids): void
    {
        Preorder::query()
            ->whereIn('id', explode(',', $ids))
            ->delete();
    }
}
