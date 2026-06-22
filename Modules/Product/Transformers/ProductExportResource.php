<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Option\Transformers\OptionResource;

class ProductExportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'price' => $this->price->amount(),
            'selling_price' => $this->selling_price->amount(),
            'qty' => $this->qty,
            'in_stock' => $this->in_stock,
            'is_active' => $this->is_active,
            // Переводы (из trait Translatable)
            'name' => $this->name,
            'description' => $this->description,
            // Бренд
            'brand' => $this->brand->name,
            // Категории
            'categories' => $this->categories->pluck('name'),
            // Изображения (используя ваши аксессоры)
            'base_image' => $this->base_image->path ?? null,
            'url' => $this->url(),
            // Наборы (Bundles)
            'attributes' => ProductAttributeResource::collection($this->attributes),
            'options' => OptionResource::collection($this->options),
            'bundles' => $this->bundles->map(function($bundle) {
                return [
                    'bundle_product_id' => $bundle->bundle_product_id,
                    'qty' => $bundle->product_qty,
                ];
            }),
        ];
    }
}
