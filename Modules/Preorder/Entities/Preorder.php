<?php

namespace Modules\Preorder\Entities;

use Illuminate\Http\Request;
use Modules\Preorder\Admin\PreorderTable;
use Modules\Product\Entities\Product;
use Modules\Support\Eloquent\Model;

class Preorder extends Model
{
    protected $table = 'preorders';

    protected $fillable = [
        'product_id',
        'phone',
        'ip_address',
        'user_agent',
    ];

    public function product()
    {
        return $this
            ->belongsTo(Product::class)
            ->withoutGlobalScope('active')
            ->withTrashed();
    }

    public function table(Request $request): PreorderTable
    {
        $query = static::query()
            ->with('product')
            ->latest();

        return new PreorderTable($query);
    }
}
