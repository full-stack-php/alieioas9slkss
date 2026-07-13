<?php

namespace Modules\Storefront\Http\ViewComposers;

use Exception;
use Illuminate\View\View;
use Spatie\SchemaOrg\Schema;
//use Modules\Compare\Compare;
use Modules\Cart\Facades\Cart;
use Modules\Menu\Entities\Menu;
use Modules\Page\Entities\Page;
use Modules\Media\Entities\File;
use Modules\Menu\MegaMenu\MegaMenu;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\SearchTerm;

class LayoutComposer
{
    /**
     * @var Compare
     */
    private $compare;


    /**
     * Create a new view composer instance.
     *
     * @param Compare $compare
     */
//    public function __construct(Compare $compare)
    public function __construct()
    {
//        $this->compare = $compare;
    }


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
//            'compareCount' => $this->compare->count(),
//            'wishlistCount' => $this->getWishlistCount(),
            'cartQuantity' => $this->getCartQuantity(),
            'favicon' => $this->getFavicon(),
            'logo' => $this->getHeaderLogo(),
            'footer_logo' => $this->getFooterLogo(),
            'newsletterBgImage' => $this->getNewsletterBgImage(),
            'privacyPageUrl' => $this->getPrivacyPageUrl(),
            'categories' => $this->getCategories(),
            'mostSearchedKeywords' => $this->getMostSearchedKeywords(),
            'primaryMenu' => $this->getPrimaryMenu(),
            'categoryMenu' => $this->getCategoryMenu(),
            'footerMenuOne' => $this->getFooterMenuOne(),
            'footerMenuTwo' => $this->getFooterMenuTwo(),
            'footerMenuThree' => $this->getFooterMenuThree(),
            'copyrightText' => $this->getCopyrightText(),
            'acceptedPaymentMethodsImage' => $this->getAcceptedPaymentMethodsImage(),
            'schemaMarkup' => $this->getSchemaMarkup(),
            'contactData' => $this->getContactData(),
            'repeat_btn' => $this->getRepeatBtn(),
            'mobileMenuWorkingHours' => $this->getMobileMenuWorkingHours(),
            'mobileMenuSocialLinks' => $this->getMobileMenuSocialLinks(),
            'schemaOrganization' => $this->getOrganizationSchema(),
            'schemaShipping' => $this->getShippingSchema(),
        ]);
    }


    private function getWishlistCount()
    {
        return auth()->check() ? auth()->user()->wishlist()->get()->count() : 0;
    }


    private function getCartQuantity()
    {
        return Cart::instance()->count();
    }



    private function getFavicon()
    {
        return $this->getMedia(setting('storefront_favicon'))->path;
    }


    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }


    private function getHeaderLogo()
    {
        return $this->getMedia(setting('storefront_header_logo'))->path;
    }
    private function getFooterLogo()
    {
        return $this->getMedia(setting('storefront_footer_logo'))->path;
    }


    private function getNewsletterBgImage()
    {
        return $this->getMedia(setting('storefront_newsletter_bg_image'))->path;
    }


    private function getPrivacyPageUrl()
    {
        return Cache::tags('settings')->rememberForever('privacy_page_url', function () {
            return Page::urlForPage(setting('storefront_privacy_page'));
        });
    }


    private function getCategories()
    {
        return Category::searchable();
    }


    private function getMostSearchedKeywords()
    {
        return Cache::remember('most_searched_keywords', now()->addHour(), function () {
            return SearchTerm::select('term')->orderByDesc('hits')->take(5)->pluck('term');
        });
    }


    private function getPrimaryMenu()
    {
        return new MegaMenu(setting('storefront_primary_menu'));
    }


    private function getCategoryMenu()
    {
        return new MegaMenu(setting('storefront_catalog_menu'));
    }


    private function getFooterMenuOne()
    {
        return $this->getFooterMenu(setting('storefront_footer_menu_one'));
    }

    private function getFooterMenuThree()
    {
        return $this->getFooterMenu(setting('storefront_footer_menu_three'));
    }


    private function getFooterMenu($menuId)
    {
        return Cache::tags(['menu_items', 'categories', 'pages', 'settings'])
            ->rememberForever(md5("storefront_footer_menu.{$menuId}:" . locale()), function () use ($menuId) {
                return Menu::for($menuId);
            });
    }


    private function getFooterMenuTwo()
    {
        return $this->getFooterMenu(setting('storefront_footer_menu_two'));
    }



    private function getCopyrightText()
    {
        return strtr(setting('storefront_copyright_text'), [
            '{{ store_url }}' => route('home'),
            '{{ store_name }}' => setting('store_name'),
            '{{ year }}' => date('Y'),
        ]);
    }


    private function getAcceptedPaymentMethodsImage()
    {
        return $this->getMedia(setting('storefront_accepted_payment_methods_image'));
    }


    private function getSchemaMarkup()
    {
        return Schema::webSite()
            ->name(setting('store_name') ?? 'Superlens')
            ->alternateName(setting('store_name') ?? 'Superlens')
            ->url(route('home'))
            ->potentialAction($this->searchActionSchema());
    }


    private function getOrganizationSchema()
    {
        return Schema::organization()
            ->name(setting('store_name') ?? 'Superlens')
            ->url(route('home'))
            ->logo(asset($this->getHeaderLogo()));
    }

    private function getShippingSchema()
    {
        return Schema::offerShippingDetails()
            ->deliveryTime(
                Schema::shippingDeliveryTime()
                    ->businessDays(
                        Schema::openingHoursSpecification()
                            ->dayOfWeek([
                                "https://schema.org/Monday",
                                "https://schema.org/Tuesday",
                                "https://schema.org/Wednesday",
                                "https://schema.org/Thursday",
                                "https://schema.org/Friday"
                            ])
                            ->opens("09:00")
                            ->closes("18:00")
                    )
                    ->cutoffTime("12:00:00Z")
                    ->handlingTime(
                        Schema::quantitativeValue()
                            ->minValue(1)
                            ->maxValue(2)
                            ->unitText("business day")
                    )
                    ->transitTime(
                        Schema::quantitativeValue()
                            ->minValue(2)
                            ->maxValue(5)
                            ->unitText("business day")
                    )
            )
            ->shippingDestination(
                Schema::definedRegion()
                    ->addressCountry("US")
            )
            ->shippingRate(
                Schema::monetaryAmount()
                    ->value("2623.00")
                    ->currency("USD")
            )
            ->url(route('home'));
    }


    private function searchActionSchema()
    {
        return Schema::searchAction()
            ->target(route('products.index') . '?query={search_term_string}')
            ->setProperty('query-input', 'required name=search_term_string');
    }



    private function getContactData()
    {
        return [
            'phone_1' => setting('store_phone')?? false,
            'phone_2' => setting('store_phone2')?? false,
            'phone_3' => setting('store_phone3')?? false,
            'openTime' => setting('storefront_opentime')?? false,
            'addressHeader' => setting('storefront_address')?? false,
            'showCallBackForm' => setting('storefront_show_callback_btn')?? false,
            'facebook' => setting('storefront_facebook_link')?? false,
            'viber' => setting('storefront_viber_link')?? false,
            'telegram' => setting('storefront_telegram_link')?? false,
            'whatsapp' => setting('storefront_whatsapp_link')?? false,
            'footer_open_time' => setting('storefront_footer_open_time')?? false,
            'footer_address' => setting('storefront_footer_address')?? false,
            'store_email' => setting('store_email')?? false,
        ];

    }


    private function getRepeatBtn()
    {
        return setting('storefront_show_repeat_btn')?? false;
    }

    private function getMobileMenuSocialLinks()
    {
        return collect([
            'facebook' => [
                'name' => trans('storefront::storefront.social_links.facebook'),
                'url' => setting('storefront_facebook_page_link'),
                'icon' => 'facebook',
            ],
            'instagram' => [
                'name' => trans('storefront::storefront.social_links.instagram'),
                'url' => setting('storefront_instagram_link'),
                'icon' => 'instagram',
            ],
            'youtube' => [
                'name' => trans('storefront::storefront.social_links.youtube'),
                'url' => setting('storefront_youtube_link'),
                'icon' => 'youtube',
            ],
        ])->filter(function ($item) {
            return !empty($item['url']);
        });
    }

    private function getMobileMenuWorkingHours()
    {
        return setting('storefront_mobile_menu_working_hours');
    }
}
