<?php

namespace Modules\Product\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Tag\Entities\Tag;
use Modules\Brand\Entities\Brand;
use Modules\Tax\Entities\TaxClass;
use Modules\Category\Entities\Category;

class ProductTabs extends Tabs
{
    public function make()
    {
        $this->group('basic_information', trans('product::products.tabs.group.basic_information'))
            ->active()
            ->add($this->general())
            ->add($this->price())
            ->add($this->inventory())
            ->add($this->images())
            ->add($this->colorProducts())
            ->add($this->packagings())
            ->add($this->gifts())
            ->add($this->bundles())
            ->add($this->relatedProducts())
            ->add($this->videos())
            ->add($this->documents())
            ->add($this->crossSells())
            ->add($this->additional())
            ->add($this->faq())
            ->add($this->seo());
    }

    private function general()
    {
        return tap(new Tab('general', trans('product::products.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields(['name', 'description', 'brand_id', 'tax_class_id', 'is_active']);
            $tab->view('product::admin.products.tabs.general', [
                'brands' => $this->brands(),
                'categories' => Category::treeList(),
            ]);
        });
    }

    private function brands()
    {
        return Brand::list()->prepend(trans('admin::admin.form.please_select'), '');
    }


    private function price()
    {
        return tap(new Tab('price', trans('product::products.tabs.price')), function (Tab $tab) {
            $tab->weight(10);

            $tab->fields([
                'price',
                'special_price',
                'special_price_type',
                'special_price_start',
                'special_price_end',
            ]);

            $tab->view('product::admin.products.tabs.price');
        });
    }

    private function inventory()
    {
        return tap(new Tab('inventory', trans('product::products.tabs.inventory')), function (Tab $tab) {
            $tab->weight(15);
            $tab->fields(['manage_stock', 'qty', 'in_stock']);
            $tab->view('product::admin.products.tabs.inventory');
        });
    }

    private function images()
    {
        if (! auth()->user()->hasAccess('admin.media.index')) {
            return;
        }

        return tap(new Tab('images', trans('product::products.tabs.images')), function (Tab $tab) {
            $tab->weight(20);
            $tab->view('product::admin.products.tabs.images');
        });
    }
    private function packagings()
    {
        return tap(new Tab('packaging', trans('product::products.tabs.packaging')), function (Tab $tab) {
            $tab->weight(30);
            $tab->view('product::admin.products.tabs.packaging');
        });
    }
    private function gifts()
    {
        return tap(new Tab('gifts', trans('product::products.tabs.gifts')), function (Tab $tab) {
            $tab->weight(45);
            $tab->view('product::admin.products.tabs.gifts');
        });
    }
    private function bundles()
    {
        return tap(new Tab('bundles', trans('product::products.tabs.bundles')), function (Tab $tab) {
            $tab->weight(40);
            $tab->view('product::admin.products.tabs.product_bundles');
        });
    }

    private function seo()
    {
        return tap(new Tab('seo', trans('product::products.tabs.seo')), function (Tab $tab) {
            $tab->weight(55);
            $tab->fields(['slug']);
            $tab->view('product::admin.products.tabs.seo');
        });
    }

    private function relatedProducts()
    {
        return tap(new Tab('related_products', trans('product::products.tabs.related_products')), function (Tab $tab) {
            $tab->weight(65);
            $tab->view('product::admin.products.tabs.products', $this->productPickerData('related_products'));
        });
    }

//    private function relatedProducts()
//    {
//        return tap(new Tab('related_products', trans('product::products.tabs.related_products')), function (Tab $tab) {
//            $tab->weight(65);
//            $tab->view('product::admin.products.tabs.products', ['name' => 'related_products']);
//        });
//    }

    private function colorProducts()
    {
        return tap(new Tab('colors', trans('product::products.tabs.colors')), function (Tab $tab) {
            $tab->weight(40);
            $tab->view('product::admin.products.tabs.products', $this->productPickerData('colors'));
        });
    }
//    private function colorProducts()
//    {
//        return tap(new Tab('colors', trans('product::products.tabs.colors')), function (Tab $tab) {
//            $tab->weight(40);
//            $tab->view('product::admin.products.tabs.products', ['name' => 'colors']);
//        });
//    }

    private function crossSells()
    {
        return tap(new Tab('cross_sells', trans('product::products.tabs.cross_sells')), function (Tab $tab) {
            $tab->weight(65);
            $tab->view('product::admin.products.tabs.products', $this->productPickerData('cross_sells'));
        });
    }

//    private function crossSells()
//    {
//        return tap(new Tab('cross_sells', trans('product::products.tabs.cross_sells')), function (Tab $tab) {
//            $tab->weight(65);
//            $tab->view('product::admin.products.tabs.products', ['name' => 'cross_sells']);
//        });
//    }

    private function additional()
    {
        return tap(new Tab('additional', trans('product::products.tabs.additional')), function (Tab $tab) {
            $tab->weight(65);
            $tab->fields(['new_from', 'new_to']);
            $tab->view('product::admin.products.tabs.additional');
        });
    }

    private function faq()
    {
        return tap(new Tab('faq', trans('blog::post.tabs.faq')), function (Tab $tab) {
            $tab->weight(90);
            $tab->view('product::admin.products.tabs.faq');
        });
    }

    private function statuses()
    {
        return collect([
            '' => trans('admin::admin.form.please_select'),
            '1' => trans('admin::admin.table.active'),
            '0' => trans('admin::admin.table.inactive'),
        ]);
    }

    private function filterCategories()
    {
        return ['' => trans('admin::admin.form.please_select')] + Category::treeList();
    }

    private function productPickerData(string $name): array
    {
        return [
            'name' => $name,
            'categories' => $this->filterCategories(),
            'brands' => $this->brands(),
            'statuses' => $this->statuses(),
        ];
    }
    private function videos()
    {
        return tap(new Tab('videos', trans('product::products.tabs.videos')), function (Tab $tab) {
            $tab->weight(25);
            $tab->view('product::admin.products.tabs.videos');
        });
    }

    private function documents()
    {
        return tap(new Tab('documents', trans('product::products.tabs.documents')), function (Tab $tab) {
            $tab->weight(27);
            $tab->view('product::admin.products.tabs.documents');
        });
    }
}
