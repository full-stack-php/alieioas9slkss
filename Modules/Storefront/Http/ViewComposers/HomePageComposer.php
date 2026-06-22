<?php

namespace Modules\Storefront\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Blog\Entities\BlogCategory;
use Modules\Product\Entities\Product;
use Modules\Storefront\Banner;
use Modules\Storefront\Feature;
use Modules\Brand\Entities\Brand;
use Illuminate\Support\Collection;
use Modules\Blog\Entities\BlogPost;
use Modules\Slider\Entities\Slider;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\Category;

class HomePageComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose($view)
    {
        $view->with([
            'slider' => Slider::findWithSlides(setting('storefront_slider')),
            'secondSlider' => Slider::findWithSlides(setting('storefront_second_slider')),
            'sliderBanners' => Banner::getSliderBanners(),
            'features' => Feature::all(),
            'threeColumnBanners' => $this->threeColumnBanners(),
            'featuredCategories' => $this->featuredCategoriesSection(),
            'productTabsOne' => $this->productTabsOne(),

            'oneColumnBanner' => $this->oneColumnBanner(),
            'blog' => $this->blog(),
        ]);
    }


    private function featuredCategoriesSection()
    {
        if (!setting('storefront_featured_categories_section_enabled')) {
            return;
        }

        return [
            'title' => setting('storefront_featured_categories_section_title'),
            'subtitle' => setting('storefront_featured_categories_section_subtitle'),
            'categories' => $this->getFeaturedCategories(),
        ];
    }


    private function getFeaturedCategories()
    {
        $categoryIds = Collection::times(6, function ($number) {
            if (!is_null(setting("storefront_featured_categories_section_category_{$number}_product_type"))) {
                return setting("storefront_featured_categories_section_category_{$number}_category_id");
            }
        })->filter();

        return Category::with('files')
            ->whereIn('id', $categoryIds)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->orderByRaw("FIELD(id, {$categoryIds->filter()->implode(',')})");
            })
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'logo' => $category->logo,
                ];
            });
    }


    private function threeColumnFullWidthBanners()
    {
        if (setting('storefront_three_column_full_width_banners_enabled')) {
            return Banner::getThreeColumnFullWidthBanners();
        }
    }


    private function productTabsOne()
    {
        if (!setting('storefront_product_tabs_1_section_enabled')) {
            return collect(); // Возвращаем пустую коллекцию вместо null для безопасности циклов
        }

        return Collection::times(4, function ($number) {
            $settingPrefix = "storefront_product_tabs_1_section_tab_{$number}";

            if (is_null(setting("{$settingPrefix}_product_type"))) {
                return null;
            }

            return (object) [
                'title' => setting("{$settingPrefix}_title"),
                'products' => $this->getProducts($settingPrefix), // Тянем товары по префиксу
            ];
        })->filter();
    }


    protected function getProducts($settingPrefix)
    {
        $type = setting("{$settingPrefix}_product_type", 'custom_products');
        $limit = setting("{$settingPrefix}_products_limit");

        if ($type === 'category_products') {
            return $this->categoryProducts($settingPrefix, $limit);
        }

        if ($type === 'recently_viewed_products') {
            return $this->recentlyViewedProducts($limit);
        }

        return Product::when($type === 'latest_products', $this->latestProductsCallback($limit))
            ->when($type === 'custom_products', $this->customProductsCallback($settingPrefix))
            ->get();
    }


    private function categoryProducts($settingPrefix, $limit)
    {
        return Category::findOrNew(setting("{$settingPrefix}_category_id"))
            ->products()
            ->latest()
            ->forCard()
            ->take($limit)
            ->get();
    }


    private function recentlyViewedProducts($limit)
    {
        return collect($this->recentlyViewed->products())
            ->reverse()
            ->when(!is_null($limit), function (Collection $products) use ($limit) {
                return $products->take($limit);
            })
            ->values();
    }


    private function latestProductsCallback($limit)
    {
        return function ($query) use ($limit) {
            $query->latest()
                ->when(!is_null($limit), function ($q) use ($limit) {
                    $q->limit($limit);
                });
        };
    }


    private function customProductsCallback($settingPrefix)
    {
        return function ($query) use ($settingPrefix) {
            $productIds = setting("{$settingPrefix}_products", []);

            $query->whereIn('id', $productIds)
                ->when(!empty($productIds), function ($q) use ($productIds) {
                    $productIdsString = collect($productIds)->filter()->implode(',');

                    $q->orderByRaw("FIELD(id, {$productIdsString})");
                });
        };
    }


    private function topBrands()
    {
        if (!setting('storefront_top_brands_section_enabled')) {
            return collect();
        }

        $topBrandIds = setting('storefront_top_brands', []);

        return Cache::rememberForever(md5('storefront_top_brands:' . serialize($topBrandIds)), function () use ($topBrandIds) {
            return Brand::with('files')
                ->whereIn('id', $topBrandIds)
                ->when(!empty($topBrandIds), function ($query) use ($topBrandIds) {
                    $topBrandIdsString = collect($topBrandIds)->filter()->implode(',');

                    $query->orderByRaw("FIELD(id, {$topBrandIdsString})");
                })
                ->get()
                ->map(function (Brand $brand) {
                    return [
                        'url' => $brand->url(),
                        'logo' => $brand->getLogoAttribute(),
                    ];
                });
        });
    }


    private function flashSale()
    {
        return [
            'title' => setting('storefront_flash_sale_title'),
            'vertical_products_1_title' => setting('storefront_vertical_products_1_title'),
            'vertical_products_2_title' => setting('storefront_vertical_products_2_title'),
            'vertical_products_3_title' => setting('storefront_vertical_products_3_title'),
        ];
    }


    private function twoColumnBanners()
    {
        if (setting('storefront_two_column_banners_enabled')) {
            return Banner::getTwoColumnBanners();
        }
    }


    private function gridProducts()
    {
        if (!setting('storefront_product_grid_section_enabled')) {
            return;
        }

        return Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_grid_section_tab_{$number}_product_type"))) {
                return setting("storefront_product_grid_section_tab_{$number}_title");
            }
        })->filter();
    }


    private function threeColumnBanners()
    {
        if (setting('storefront_three_column_banners_enabled')) {
            return Banner::getFourColumnBanners();
        }
    }


    private function productTabsTwo()
    {
        if (!setting('storefront_product_tabs_2_section_enabled')) {
            return;
        }

        $tabs = Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_tabs_2_section_tab_{$number}_product_type"))) {
                return setting("storefront_product_tabs_2_section_tab_{$number}_title");
            }
        })->filter();

        return [
            'title' => setting('storefront_product_tabs_2_section_title'),
            'tabs' => $tabs,
        ];
    }


    private function oneColumnBanner()
    {
        if (setting('storefront_one_column_banner_enabled')) {
            return Banner::getOneColumnBanner();
        }
    }


    private function blog()
    {
        if (setting('storefront_blogs_section_enabled')) {
            $categoryId = setting('storefront_blog_category');

            if (is_null($categoryId)) {
                return;
            }

            $blogCategory = BlogCategory::findOrFail($categoryId);

            $categoryIds = array_merge([$blogCategory->id], $blogCategory->getDescendantsIds());

            $blogPosts = BlogPost::published()
                ->whereIn('blog_category_id', $categoryIds)
                ->latest()
                ->take(setting('storefront_recent_blogs') ?? 10)
                ->get();

            return [
                'title' => setting('storefront_blogs_section_title'),
                'blogPosts' => $blogPosts,
            ];
        }
    }
}
