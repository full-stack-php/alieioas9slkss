<?php

namespace Modules\Storefront\Http\ViewComposers;

use Illuminate\View\View;
use Spatie\SchemaOrg\Schema;
use Modules\Storefront\Banner;
use Modules\Storefront\Feature;
use Illuminate\Support\Collection;
use Modules\Product\Entities\Product;
use Spatie\SchemaOrg\ItemAvailability;

class ProductShowPageComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $product = $view->getData()['product'];

        $view->with([
            'features' => Feature::all(),
            'optionSets' => $this->divideOption($product),
            'banner' => Banner::getProductPageBanner(),
            'productSchemaMarkup' => $this->schemaMarkup($product),
            'categoryBreadcrumb' => $this->getCategoryBreadCrumb($product->categories->nest()),
            'allProductNotification' => setting('storefront_product_notify_message_status') ? setting('storefront_product_notify_message') : false,
            'productAvailability' => [
                'is_preorder' => $product->isPreorder(),
                'is_discontinued' => $product->isDiscontinued(),
                'is_purchasable' => $product->isPurchasable(),
            ],
        ]);
    }


    private function schemaMarkup(Product $product)
    {
        $schema = Schema::product()
            ->name($product->name)
            ->sku($product->sku)
            ->url($product->url())
            ->image($product->base_image->path)
            ->brand($this->brandSchema($product))
            ->description($product->short_description)
            ->offers($this->offersSchema($product));

        if ($product->reviews()->count() > 0) {
            $schema->aggregateRating($this->aggregateRatingSchema($product));
        }

        return $schema;
    }


    private function brandSchema(Product $product)
    {
        return Schema::brand()->name($product->brand->name);
    }


    private function aggregateRatingSchema(Product $product)
    {
        return Schema::aggregateRating()
            ->ratingValue($product->reviews()->avg('rating'))
            ->ratingCount($product->reviews()->count());
    }


    private function offersSchema(Product $product)
    {
        return Schema::offer()
            ->price(
                ($product->variant ?? $product)
                    ->selling_price
                    ->convertToCurrentCurrency()
                    ->amount()
            )
            ->priceCurrency(currency())
            ->availability($this->itemAvailability($product))
            ->url($product->url());
    }

    private function itemAvailability(Product $product): string
    {
        if ($product->isPreorder()) {
            return ItemAvailability::PreOrder;
        }

        if ($product->isDiscontinued()) {
            return ItemAvailability::Discontinued;
        }

        return $product->isInStock()
            ? ItemAvailability::InStock
            : ItemAvailability::OutOfStock;
    }

    private function getCategoryBreadCrumb(Collection $categories)
    {
        $breadcrumb = '';

        foreach ($categories as $category) {
            $breadcrumb .= "<li><a href='{$category->url()}'>{$category->name}</a></li>";

            if ($category->items->isNotEmpty()) {
                $breadcrumb .= $this->getCategoryBreadCrumb($category->items);
            }
        }

        return $breadcrumb;
    }

    private function divideOption(Product $product){
        $exludeOptionIds = setting('product_mirrored_options') ?? [];

        $options = false;
        $mirrored = false;

        foreach ($product->options as $k => $option) {
            if(in_array($option->option_id, $exludeOptionIds)){
                $mirrored[$k] = $option;
            } else {
                $options[$k] = $option;
            }
        }

        return [
            'options' => $options,
            'mirrored' => $mirrored
        ];
    }
}
