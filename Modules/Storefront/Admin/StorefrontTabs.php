<?php

namespace Modules\Storefront\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Blog\Entities\BlogCategory;
use Modules\Category\Entities\Category;
use Modules\Option\Entities\Option;
use Modules\Storefront\Banner;
use Modules\Menu\Entities\Menu;
use Modules\Page\Entities\Page;
use Modules\Media\Entities\File;
use Modules\Brand\Entities\Brand;
use Modules\Slider\Entities\Slider;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Repositories\ProductRepository;

class StorefrontTabs extends Tabs
{
    /**
     * Make new tabs with groups.
     *
     * @return void
     */
    public function make()
    {
        $this->group('general_settings', trans('storefront::storefront.tabs.group.general_settings'))
            ->active()
            ->add($this->general())
            ->add($this->logo())
            ->add($this->menus())
            ->add($this->footer())
            ->add($this->newsletter())
            ->add($this->aiBtnsLinks())
            ->add($this->socialLinks());


        $this->group('home_page_sections', trans('storefront::storefront.tabs.group.home_page_sections'))
            ->add($this->sliderBanners())
            ->add($this->features())
            ->add($this->fourColumnBanners())
            ->add($this->productTabsOne())
            ->add($this->oneColumnBanner())
            ->add($this->googleReviews())
            ->add($this->blogs())
            ->add($this->seoData())
            ->add($this->schemaHomePageData());

        $this->group('product_page_section', trans('storefront::storefront.tabs.group.product_page_sections'))
            ->add($this->productGeneralSettings());

        $this->group('contact_page_sections', trans('storefront::storefront.tabs.group.contact_page_sections'))
            ->add($this->contactInformation());

        $this->group('schema_page_sections', trans('storefront::storefront.tabs.group.schema_page_sections'))
            ->add($this->schemaInformation());
    }


    private function general()
    {
        return tap(new Tab('general', trans('storefront::storefront.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields(['storefront_slider', 'storefront_copyright_text']);
            $tab->view('storefront::admin.storefront.tabs.general', [
                'pages' => $this->getPages(),
                'top_notify_bg' => $this->getMedia(setting('storefront_top_notify_bg')),
                'top_notify_mobile_bg' => $this->getMedia(setting('storefront_top_notify_mobile_bg')),
            ]);
        });
    }


    private function getPages()
    {
        return Page::all()->pluck('name', 'id')
            ->prepend(trans('storefront::storefront.form.please_select'), '');
    }


    private function getSliders()
    {
        return Slider::all()->sortBy('name')->pluck('name', 'id')
            ->prepend(trans('storefront::storefront.form.please_select'), '');
    }


    private function logo()
    {
        return tap(new Tab('logo', trans('storefront::storefront.tabs.logo')), function (Tab $tab) {
            $tab->weight(10);
            $tab->view('storefront::admin.storefront.tabs.logo', [
                'favicon' => $this->getMedia(setting('storefront_favicon')),
                'headerLogo' => $this->getMedia(setting('storefront_header_logo')),
                'footerLogo' => $this->getMedia(setting('storefront_footer_logo')),
                'mailLogo' => $this->getMedia(setting('storefront_mail_logo')),
            ]);
        });
    }


    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }


    private function menus()
    {
        return tap(new Tab('menus', trans('storefront::storefront.tabs.menus')), function (Tab $tab) {
            $tab->weight(15);

            $tab->fields([
                'storefront_catalog_menu',
                'storefront_primary_menu',
                'storefront_footer_menu_one',
                'storefront_footer_menu_two',
                'storefront_footer_menu_three',
                'storefront_mobile_menu',
                'storefront_mobile_menu_working_hours',
            ]);

            $tab->view('storefront::admin.storefront.tabs.menus', [
                'menus' => $this->getMenus(),
            ]);
        });
    }


    private function getMenus()
    {
        return Menu::all()->pluck('name', 'id')
            ->prepend(trans('storefront::storefront.form.please_select'), '');
    }

    private function getBlogCategories()
    {
        return BlogCategory::all()->pluck('name', 'id')
            ->prepend(trans('storefront::storefront.form.please_select'), '');
    }


    private function footer()
    {
        return tap(new Tab('footer', trans('storefront::storefront.tabs.footer')), function (Tab $tab) {
            $tab->weight(17);
            $tab->view('storefront::admin.storefront.tabs.footer', [
                'acceptedPaymentMethodsImage' => $this->getMedia(setting('storefront_accepted_payment_methods_image')),
            ]);
        });
    }


    private function newsletter()
    {
        if (!setting('newsletter_enabled')) {
            return;
        }

        return tap(new Tab('newsletter', trans('storefront::storefront.tabs.newsletter')), function (Tab $tab) {
            $tab->weight(18);
            $tab->view('storefront::admin.storefront.tabs.newsletter', [
                'newsletterBgImage' => $this->getMedia(setting('storefront_newsletter_bg_image')),
            ]);
        });
    }


    private function features()
    {
        return tap(new Tab('features', trans('storefront::storefront.tabs.features')), function (Tab $tab) {
            $tab->weight(20);
            $tab->view('storefront::admin.storefront.tabs.features', [
                'storefront_feature_1_icon' => $this->getMedia(setting('storefront_feature_1_icon')),
                'storefront_feature_2_icon' => $this->getMedia(setting('storefront_feature_2_icon')),
                'storefront_feature_3_icon' => $this->getMedia(setting('storefront_feature_3_icon')),
                'storefront_feature_4_icon' => $this->getMedia(setting('storefront_feature_4_icon')),
                'storefront_feature_5_icon' => $this->getMedia(setting('storefront_feature_5_icon')),
                'storefront_feature_6_icon' => $this->getMedia(setting('storefront_feature_6_icon')),
            ]);
        });
    }


    private function productPage()
    {
        return tap(new Tab('product_page', trans('storefront::storefront.tabs.product_page')), function (Tab $tab) {
            $tab->weight(22);
            $tab->view('storefront::admin.storefront.tabs.product_page', [
                'banner' => Banner::getProductPageBanner(),
            ]);
        });
    }


    private function socialLinks()
    {
        return tap(new Tab('social_links', trans('storefront::storefront.tabs.social_links')), function (Tab $tab) {
            $tab->weight(25);

            $tab->fields([
                'storefront_facebook_link',
                'storefront_facebook_page_link',
                'storefront_instagram_link',
                'storefront_youtube_link',
                'storefront_viber_link',
                'storefront_telegram_link',
                'storefront_whatsapp_link',
            ]);

            $tab->view('storefront::admin.storefront.tabs.social_links');
        });
    }

    private function aiBtnsLinks()
    {
        return tap(new Tab('ai_btns_links', trans('storefront::storefront.tabs.ai_btns_links')), function (Tab $tab) {
            $tab->weight(25);

            $tab->view('storefront::admin.storefront.tabs.ai_data');
        });
    }


    private function sliderBanners()
    {
        return tap(new Tab('slider_banners', trans('storefront::storefront.tabs.slider_banners')), function (Tab $tab) {
            $tab->active();
            $tab->weight(10);
            $tab->view('storefront::admin.storefront.tabs.slider_banners', [
                'sliders' => $this->getSliders(),
                'second_sliders' => $this->getSliders(),
                'banners' => Banner::getSliderBanners(),
            ]);
        });
    }


    private function threeColumnFullWidthBanners()
    {
        return tap(new Tab('three_column_full_width_banners', trans('storefront::storefront.tabs.three_column_full_width_banners')), function (Tab $tab) {
            $tab->weight(35);
            $tab->view('storefront::admin.storefront.tabs.three_column_full_width_banners', [
                'banners' => Banner::getThreeColumnFullWidthBanners(),
            ]);
        });
    }


    private function featuredCategories()
    {
        return tap(new Tab('featured_categories', trans('storefront::storefront.tabs.featured_categories')), function (Tab $tab) {
            $tab->weight(50);
            $tab->view('storefront::admin.storefront.tabs.featured_categories', [
                'categoryOneProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_1_products'),
                'categoryTwoProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_2_products'),
                'categoryThreeProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_3_products'),
                'categoryFourProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_4_products'),
                'categoryFiveProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_5_products'),
                'categorySixProducts' => $this->getProductListFromSetting('storefront_featured_categories_section_category_6_products'),
            ]);
        });
    }


    private function getProductListFromSetting($key)
    {
        return ProductRepository::list(setting($key, []));
    }


    private function productTabsOne()
    {
        return tap(new Tab('product_tabs_one', trans('storefront::storefront.tabs.product_tabs_one')), function (Tab $tab) {
            $tab->weight(45);
            $tab->view('storefront::admin.storefront.tabs.product_tabs_one', [
                'tabOneProducts' => $this->getProductListFromSetting('storefront_product_tabs_1_section_tab_1_products'),
            ]);
        });
    }


    private function topBrands()
    {
        if (!auth()->user()->hasAccess(['admin.brands.index'])) {
            return;
        }

        return tap(new Tab('top_brands', trans('storefront::storefront.tabs.top_brands')), function (Tab $tab) {
            $tab->weight(50);
            $tab->view('storefront::admin.storefront.tabs.top_brands', [
                'brands' => Brand::list(),
            ]);
        });
    }


    private function flashSaleAndVerticalProducts()
    {
        return tap(new Tab('flash_sale_and_vertical_products', trans('storefront::storefront.tabs.flash_sale_and_vertical_products')), function (Tab $tab) {
            $tab->weight(60);
            $tab->view('storefront::admin.storefront.tabs.flash_sale_and_vertical_products', [
                'flashSales' => $this->getFlashSales(),
                'verticalProductsOne' => $this->getProductListFromSetting('storefront_vertical_products_1_products'),
                'verticalProductsTwo' => $this->getProductListFromSetting('storefront_vertical_products_2_products'),
                'verticalProductsThree' => $this->getProductListFromSetting('storefront_vertical_products_3_products'),
            ]);
        });
    }


    private function getFlashSales()
    {
        return FlashSale::all()->pluck('campaign_name', 'id')
            ->prepend(trans('admin::admin.form.please_select'), '');
    }


    private function twoColumnBanners()
    {
        return tap(new Tab('two_column_banners', trans('storefront::storefront.tabs.two_column_banners')), function (Tab $tab) {
            $tab->weight(65);
            $tab->view('storefront::admin.storefront.tabs.two_column_banners', [
                'banners' => Banner::getTwoColumnBanners(),
            ]);
        });
    }


    private function productGrid()
    {
        return tap(new Tab('product_grid', trans('storefront::storefront.tabs.product_grid')), function (Tab $tab) {
            $tab->weight(70);
            $tab->view('storefront::admin.storefront.tabs.product_grid', [
                'tabOneProducts' => $this->getProductListFromSetting('storefront_product_grid_section_tab_1_products'),
                'tabTwoProducts' => $this->getProductListFromSetting('storefront_product_grid_section_tab_2_products'),
                'tabThreeProducts' => $this->getProductListFromSetting('storefront_product_grid_section_tab_3_products'),
                'tabFourProducts' => $this->getProductListFromSetting('storefront_product_grid_section_tab_4_products'),
            ]);
        });
    }


    private function fourColumnBanners()
    {
        return tap(new Tab('four_column_banners', trans('storefront::storefront.tabs.four_column_banners')), function (Tab $tab) {
            $tab->weight(45);
            $tab->view('storefront::admin.storefront.tabs.four_column_banners', [
                'banners' => Banner::getFourColumnBanners(),
            ]);
        });
    }


    private function productTabsTwo()
    {
        return tap(new Tab('product_tabs_two', trans('storefront::storefront.tabs.product_tabs_two')), function (Tab $tab) {
            $tab->weight(80);
            $tab->view('storefront::admin.storefront.tabs.product_tabs_two', [
                'tabOneProducts' => $this->getProductListFromSetting('storefront_product_tabs_2_section_tab_1_products'),
                'tabTwoProducts' => $this->getProductListFromSetting('storefront_product_tabs_2_section_tab_2_products'),
                'tabThreeProducts' => $this->getProductListFromSetting('storefront_product_tabs_2_section_tab_3_products'),
                'tabFourProducts' => $this->getProductListFromSetting('storefront_product_tabs_2_section_tab_4_products'),
            ]);
        });
    }


    private function oneColumnBanner()
    {
        return tap(new Tab('one_column_banner', trans('storefront::storefront.tabs.one_column_banner')), function (Tab $tab) {
            $tab->weight(80);
            $tab->view('storefront::admin.storefront.tabs.one_column_banner', [
                'banner' => Banner::getOneColumnBanner(),
            ]);
        });
    }


    private function googleReviews()
    {
        return tap(new Tab('googleReviews', trans('storefront::storefront.tabs.google_reviews')), function (Tab $tab) {
            $tab->weight(84);
            $tab->view('storefront::admin.storefront.tabs.google_reviews');
        });
    }
    private function blogs()
    {
        return tap(new Tab('blogs', trans('storefront::storefront.tabs.blogs')), function (Tab $tab) {
            $tab->weight(85);
            $tab->view('storefront::admin.storefront.tabs.blogs', [
                'blogCategories' => $this->getBlogCategories(),
            ]);
        });
    }

    private function seoData()
    {
        return tap(new Tab('seoData', trans('storefront::storefront.tabs.seo_data')), function (Tab $tab) {
            $tab->weight(90);
            $tab->view('storefront::admin.storefront.tabs.seo_data');
        });
    }
    private function schemaHomePageData()
    {
        return tap(new Tab('schemaHomePageData', trans('storefront::storefront.tabs.schema_home_page_data')), function (Tab $tab) {
            $tab->weight(90);
            $tab->view('storefront::admin.storefront.tabs.schema_homepage');
        });
    }


    private function productGeneralSettings()
    {
        return tap(new Tab('product_general', trans('storefront::storefront.tabs.product_page_general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->view('storefront::admin.storefront.tabs.product_page_general', [
                'options' => Option::all()->pluck('name', 'id'),
                'categories' => Category::all()->pluck('name', 'id'),
            ]);
        });
    }
    private function contactInformation()
    {
        return tap(new Tab('contact_general', trans('storefront::storefront.tabs.contact_general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->view('storefront::admin.storefront.tabs.contact_address', [
                'contactBg' => $this->getMedia(setting('storefront_contact_bg')),
            ]);
        });
    }
    private function schemaInformation()
    {
        return tap(new Tab('schema_general', trans('storefront::storefront.tabs.schema_general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->view('storefront::admin.storefront.tabs.schema');
        });
    }
}
