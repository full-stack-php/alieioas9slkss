<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Request;
use Modules\Option\Transformers\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Variation\Transformers\VariationResource;

class ProductEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'brand_id' => $this->brand_id ?? '',
            'manufacturer_id' => $this->manufacturer_id ?? '',
            'categories' => $this->categories->pluck('id'),
            'attributes' => ProductAttributeResource::collection($this->attributes),
            'options' => OptionResource::collection($this->options),
            'media' => $this->filterFiles(['base_image', 'additional_images'])->get()->map->only('id', 'path'),
            'price' => $this->price?->convertToCurrentCurrency()->amount(),
            'tax_class_id' => $this->tax_class_id ?? '',
            'sku' => $this->sku,
            'manage_stock' => $this->manage_stock,
            'stock_status' => $this->stock_status,
            'qty' => $this->qty,
            'in_stock' => $this->in_stock,
            'special_price_type' => $this->special_price_type,
            'special_price' => $this->special_price?->convertToCurrentCurrency()->amount(),
            'new_from' => $this->new_from,
            'new_to' => $this->new_to,
            'up_sells' => $this->upSellProducts()->orderByPivot('created_at', 'asc')->pluck('id'),
            'cross_sells' => $this->crossSellProducts()->orderByPivot('created_at', 'asc')->pluck('id'),
            'related_products' => $this->relatedProducts()->orderByPivot('created_at', 'asc')->pluck('id'),
            'special_price_start' => $this->special_price_start,
            'special_price_end' => $this->special_price_end,
            'meta' => [
                'meta_title' => $this->meta->meta_title,
                'meta_description' => $this->meta->meta_description,
            ],
            'is_active' => $this->is_active,
        ];
    }
}
