<?php

namespace Modules\Setting\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Order\Entities\OrderStatus;
use Modules\Support\Locale;
use Modules\Support\Country;
use Modules\Support\TimeZone;
use Modules\Currency\Currency;
use Modules\User\Entities\Role;
use Modules\Media\Entities\File;
use Modules\Payment\Gateways\Iyzico;
use Illuminate\Support\Facades\Cache;

class SettingTabs extends Tabs
{
    /**
     * Make new tabs with groups.
     *
     * @return void
     */
    public function make()
    {
        $this->group('general_settings', trans('setting::settings.tabs.group.general_settings'))
            ->active()
            ->add($this->general())
            ->add($this->logo())
            ->add($this->maintenance())
            ->add($this->store())
            ->add($this->currency())
            ->add($this->mail())
            ->add($this->order_statuses())
            ->add($this->googleRecaptcha())
            ->add($this->customCssJs());

        $this->group('social_logins', trans('setting::settings.tabs.group.social_logins'))
            ->add($this->facebook())
            ->add($this->google());

        $this->group('shipping_methods', trans('setting::settings.tabs.group.shipping_methods'))
            ->add($this->freeShipping())
            ->add($this->novaPoshta())
            ->add($this->meest())
            ->add($this->localPickup());

        $this->group('payment_methods', trans('setting::settings.tabs.group.payment_methods'))
            ->add($this->cod())
            ->add($this->mono_bank())
            ->add($this->privatbank())
            ->add($this->bankTransfer());
    }


    private function general()
    {
        return tap(new Tab('general', trans('setting::settings.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);

            $tab->fields([
                'supported_countries.*',
                'default_country',
                'default_timezone',
                'customer_role',
                'customer_group_discounts.*',
                'customer_group_upgrade_thresholds.*',
                'customer_group_upgrade_order_statuses.*',
                'customer_group_discount_display',
                'customer_group_discount_exclude_special_products',
            ]);

            $tab->view('setting::admin.settings.tabs.general', [
                'countries' => Country::all(),
                'timeZones' => TimeZone::all(),
                'roles' => Role::customerGroupList(),
                'orderStatuses' => OrderStatus::listStatuses(),
            ]);
        });
    }

    private function logo()
    {
        return tap(new Tab('logo', trans('setting::settings.tabs.logo')), function (Tab $tab) {
            $tab->weight(10);
            $tab->view('setting::admin.settings.tabs.logo', [
                'logo' => $this->getMedia(setting('admin_logo')),
                'shortLogo' => $this->getMedia(setting('admin_small_logo')),
            ]);
        });
    }


    private function maintenance()
    {
        return tap(new Tab('maintenance', trans('setting::settings.tabs.maintenance')), function (Tab $tab) {
            $tab->weight(7);

            $tab->view('setting::admin.settings.tabs.maintenance');
        });
    }


    private function store()
    {
        return tap(new Tab('store', trans('setting::settings.tabs.store')), function (Tab $tab) {
            $tab->weight(10);

            $tab->fields(['translatable.store_name', 'translatable.store_tagline', 'store_phone', 'store_email', 'store_address_1', 'store_address_2', 'store_city', 'store_country', 'store_state', 'store_zip']);

            $tab->view('setting::admin.settings.tabs.store', [
                'countries' => Country::all(),
            ]);
        });
    }

    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }


    private function order_statuses()
    {
        return tap(new Tab('order_statuses', trans('setting::settings.tabs.order_statuses')), function (Tab $tab) {
            $tab->weight(20);

            $tab->fields(['default_order_status', 'canceled_order_status', 'completed_order_status', 'pending_order_status', 'pending_payment_order_status', 'complete_payment_order_status', 'refunded_order_status', ]);

            $order_statuses = ['' => trans('setting::settings.form.select_order_status')];
            $order_statuses += $this->getOrderStatuses();

            $tab->view('setting::admin.settings.tabs.order_statuses', [
                'order_statuses' => $order_statuses,
            ]);
        });
    }

    private function currency()
    {
        return tap(new Tab('currency', trans('setting::settings.tabs.currency')), function (Tab $tab) {
            $tab->weight(20);

            $tab->fields(['supported_currencies.*', 'default_currency', 'currency_rate_exchange_service', 'fixer_access_key', 'forge_api_key', 'currency_data_feed_api_key', 'auto_refresh_currency_rates', 'auto_refresh_currency_rate_frequency']);

            $tab->view('setting::admin.settings.tabs.currency', [
                'currencies' => Currency::names(),
                'currencyRateExchangeServices' => $this->getCurrencyRateExchangeServices(),
            ]);
        });
    }


    private function getCurrencyRateExchangeServices()
    {
        $currencyRateExchangeServices = ['' => trans('setting::settings.form.select_service')];

        $currencyRateExchangeServices += trans('currency::services');

        return $currencyRateExchangeServices;
    }


    private function mail()
    {
        return tap(new Tab('mail', trans('setting::settings.tabs.mail')), function (Tab $tab) {
            $tab->weight(30);

            $tab->fields(['mail_from_address']);

            $tab->view('setting::admin.settings.tabs.mail', [
                'encryptionProtocols' => $this->getMailEncryptionProtocols(),
                'orderStatuses' => trans('order::statuses'),
            ]);
        });
    }


    private function getMailEncryptionProtocols()
    {
        return ['' => trans('admin::admin.form.please_select')] + trans('setting::settings.form.mail_encryption_protocols');
    }


    private function newsletter()
    {
        return tap(new Tab('newsletter', trans('setting::settings.tabs.newsletter')), function (Tab $tab) {
            $tab->weight(32);

            $tab->fields(['newsletter_enabled', 'mailchimp_api_key', 'mailchimp_list_id']);

            $tab->view('setting::admin.settings.tabs.newsletter');
        });
    }


    private function googleRecaptcha()
    {
        return tap(new Tab('google_recaptcha', trans('setting::settings.tabs.google_recaptcha')), function (Tab $tab) {
            $tab->weight(35);

            $tab->fields(['google_recaptcha_enabled', 'google_recaptcha_site_key', 'google_recaptcha_secret_key']);

            $tab->view('setting::admin.settings.tabs.google_recaptcha');
        });
    }


    private function customCssJs()
    {
        return tap(new Tab('custom_css_js', trans('setting::settings.tabs.custom_css_js')), function (Tab $tab) {
            $tab->weight(40);

            $tab->view('setting::admin.settings.tabs.custom_css_js');
        });
    }


    private function facebook()
    {
        return tap(new Tab('facebook', trans('setting::settings.tabs.facebook')), function (Tab $tab) {
            $tab->active();
            $tab->weight(41);

            $tab->fields(['facebook_login_enabled', 'translatable.facebook_login_label', 'facebook_login_app_id', 'facebook_login_app_secret']);

            $tab->view('setting::admin.settings.tabs.facebook');
        });
    }


    private function google()
    {
        return tap(new Tab('google', trans('setting::settings.tabs.google')), function (Tab $tab) {
            $tab->weight(42);

            $tab->fields(['google_login_enabled', 'translatable.google_login_label', 'google_login_client_id', 'google_login_client_secret']);

            $tab->view('setting::admin.settings.tabs.google');
        });
    }


    private function freeShipping()
    {
        return tap(new Tab('free_shipping', trans('setting::settings.tabs.free_shipping')), function (Tab $tab) {
            $tab->active();
            $tab->weight(50);

            $tab->fields(['free_shipping_enabled', 'translatable.free_shipping_label']);

            $tab->view('setting::admin.settings.tabs.free_shipping');
        });
    }


    private function novaPoshta()
    {
        return tap(new Tab('novaPoshta', trans('setting::settings.tabs.novaPoshta')), function (Tab $tab) {
            $tab->weight(50);

            $tab->fields(['novaPoshta_enabled', 'translatable.novaPoshta_label']);

            $tab->view('setting::admin.settings.tabs.novaPoshta');
        });
    }
    private function meest()
    {
        return tap(new Tab('meest', trans('setting::settings.tabs.meest')), function (Tab $tab) {
            $tab->weight(50);

            $tab->fields(['meest_enabled', 'translatable.meest_label']);

            $tab->view('setting::admin.settings.tabs.meest');
        });
    }


    private function localPickup()
    {
        return tap(new Tab('local_pickup', trans('setting::settings.tabs.local_pickup')), function (Tab $tab) {
            $tab->weight(55);

            $tab->fields(['local_pickup_enabled', 'translatable.local_pickup_label', 'local_pickup_cost']);

            $tab->view('setting::admin.settings.tabs.local_pickup');
        });
    }

    private function mono_bank()
    {
        return tap(new Tab('mono_bank', trans('setting::settings.tabs.mono_bank')), function (Tab $tab) {
            $tab->active();
            $tab->weight(61);

            $tab->fields(['mono_bank_enabled', 'translatable.mono_bank_label', 'mono_bank_env', 'mono_bank_client_id', 'mono_bank_secret']);

            $tab->view('setting::admin.settings.tabs.mono_bank');
        });
    }
    private function privatbank()
    {
        return tap(new Tab('privatbank', trans('setting::settings.tabs.privatbank')), function (Tab $tab) {
            $tab->weight(61);

            $tab->fields(['privatbank_enabled', 'translatable.privatbank_label', 'privatbank_env', 'privatbank_client_id', 'privatbank_secret']);

            $tab->view('setting::admin.settings.tabs.privatbank');
        });
    }

    private function cod()
    {
        return tap(new Tab('cod', trans('setting::settings.tabs.cod')), function (Tab $tab) {
            $tab->weight(72);

            $tab->fields(['cod_enabled', 'translatable.cod_label', 'translatable.cod_description']);

            $tab->view('setting::admin.settings.tabs.cod');
        });
    }


    private function bankTransfer()
    {
        return tap(new Tab('bank_transfer', trans('setting::settings.tabs.bank_transfer')), function (Tab $tab) {
            $tab->weight(73);

            $tab->fields(['bank_transfer_enabled', 'translatable.bank_transfer_label', 'translatable.bank_transfer_description', 'translatable.bank_transfer_instructions']);

            $tab->view('setting::admin.settings.tabs.bank_transfer');
        });
    }


    private function checkPayment()
    {
        return tap(new Tab('check_payment', trans('setting::settings.tabs.check_payment')), function (Tab $tab) {
            $tab->weight(74);

            $tab->fields(['check_payment_enabled', 'translatable.check_payment_label', 'translatable.check_payment_description', 'translatable.check_payment_instructions']);

            $tab->view('setting::admin.settings.tabs.check_payment');
        });
    }

    private function getOrderStatuses()
    {
        return OrderStatus::all()->pluck('name', 'id')->toArray();
    }
}
