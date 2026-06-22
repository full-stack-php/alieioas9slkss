<?php

namespace Modules\Setting\Http\Requests;

use Modules\Support\Locale;
use Modules\Support\Country;
use Modules\Support\TimeZone;
use Modules\Currency\Currency;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;

class UpdateSettingRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'setting::attributes';

    /**
     * Array of attributes that should be merged with null
     * if attribute is not found in the current request.
     *
     * @var array
     */
    private $shouldCheck = ['sms_order_statuses', 'email_order_statuses'];


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supported_countries.*' => ['required', Rule::in(Country::codes())],
            'default_country' => 'required|in_array:supported_countries.*',
            'default_timezone' => ['required', Rule::in(TimeZone::all())],
            'customer_role' => ['required', Rule::exists('roles', 'id')],
            'supported_currencies.*' => ['required', Rule::in(Currency::codes())],
            'default_currency' => 'required|in_array:supported_currencies.*',

            'translatable.store_name' => 'required',
            'store_phone' => ['required'],
            'store_email' => 'required|email',
            'store_country' => ['required', Rule::in(Country::codes())],

            'fixer_access_key' => 'required_if:currency_rate_exchange_service,fixer',
            'forge_api_key' => 'required_if:currency_rate_exchange_service,forge',
            'currency_data_feed_api_key' => 'required_if:currency_rate_exchange_service,currency_data_feed',
            'auto_refresh_currency_rates' => 'required|boolean',
            'auto_refresh_currency_rate_frequency' => [
                'required_if:auto_refresh_currency_rates,1',
                Rule::in($this->refreshFrequencies()),
            ],
            'vonage_key' => ['required_if:sms_service,vonage'],
            'vonage_secret' => ['required_if:sms_service,vonage'],
            'twilio_sid' => ['required_if:sms_service,twilio'],
            'twilio_token' => ['required_if:sms_service,twilio'],
//            'sms_order_statuses.*' => ['nullable', Rule::in($this->orderStatuses())],

            'mail_from_address' => 'nullable|email',
            'mail_encryption' => ['nullable', Rule::in($this->mailEncryptionProtocols())],

            'newsletter_enabled' => ['required', 'boolean'],
            'mailchimp_api_key' => ['required_if:newsletter_enabled,1'],
            'mailchimp_list_id' => ['required_if:newsletter_enabled,1'],

            'google_recaptcha_enabled' => ['required', 'boolean'],
            'google_recaptcha_site_key' => ['required_if:google_recatcha_enabled,1'],
            'google_recaptcha_secret_key' => ['required_if:google_recaptcha_enabled,1'],

            'facebook_login_enabled' => 'required|boolean',
            'facebook_login_app_id' => 'required_if:facebook_login_enabled,1',
            'facebook_login_app_secret' => 'required_if:facebook_login_enabled,1',

            'google_login_enabled' => 'required|boolean',
            'google_login_client_id' => 'required_if:google_login_enabled,1',
            'google_login_client_secret' => 'required_if:google_login_enabled,1',

            'free_shipping_enabled' => 'required|boolean',
            'free_shipping_min_amount' => 'nullable|numeric',
            'translatable.free_shipping_label' => 'required_if:free_shipping_enabled,1',

            'local_pickup_enabled' => 'required|boolean',
            'translatable.local_pickup_label' => 'required_if:local_pickup_enabled,1',
            'local_pickup_cost' => ['required_if:local_pickup_enabled,1', 'nullable', 'numeric'],

            'flat_rate_enabled' => 'required|boolean',
            'translatable.flat_rate_label' => 'required_if:flat_rate_enabled,1',
            'flat_rate_cost' => ['required_if:flat_rate_enabled,1', 'nullable', 'numeric'],


            'cod_enabled' => 'required|boolean',
            'translatable.cod_label' => 'required_if:cod_enabled,1',
            'translatable.cod_description' => 'required_if:cod_enabled,1',

            'bank_transfer_enabled' => 'required|boolean',
            'translatable.bank_transfer_label' => 'required_if:bank_transfer_enabled,1',
            'translatable.bank_transfer_description' => 'required_if:bank_transfer_enabled,1',
            'translatable.bank_transfer_instructions' => 'required_if:bank_transfer_enabled,1',

            'check_payment_enabled' => 'required|boolean',
            'translatable.check_payment_label' => 'required_if:check_payment_enabled,1',
            'translatable.check_payment_description' => 'required_if:check_payment_enabled,1',
            'translatable.check_payment_instructions' => 'required_if:check_payment_enabled,1',
        ];
    }


    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        foreach ($this->shouldCheck as $attribute) {
            if (!$this->has($attribute)) {
                $this->merge([$attribute => null]);
            }
        }

        return $this->all();
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'reviews_enabled' => $this->has('reviews_enabled') ? $this->get('reviews_enabled') === 'on' : false,
            'auto_approve_reviews' => $this->has('auto_approve_reviews') ? $this->get('auto_approve_reviews') === 'on' : false,
            'cookie_bar_enabled' => $this->has('cookie_bar_enabled') ? $this->get('cookie_bar_enabled') === 'on' : false,
            'maintenance_mode' => $this->has('maintenance_mode') ? $this->get('maintenance_mode') === 'on' : false,
            'store_phone_hide' => $this->has('store_phone_hide') ? $this->get('store_phone_hide') === 'on' : false,
            'store_email_hide' => $this->has('store_email_hide') ? $this->get('store_email_hide') === 'on' : false,
            'auto_refresh_currency_rates' => $this->has('auto_refresh_currency_rates') ? $this->get('auto_refresh_currency_rates') === 'on' : false,
            'newsletter_enabled' => $this->has('newsletter_enabled') ? $this->get('newsletter_enabled') === 'on' : false,
            'google_recaptcha_enabled' => $this->has('google_recaptcha_enabled') ? $this->get('google_recaptcha_enabled') === 'on' : false,
            'facebook_login_enabled' => $this->has('facebook_login_enabled') ? $this->get('facebook_login_enabled') === 'on' : false,
            'google_login_enabled' => $this->has('google_login_enabled') ? $this->get('google_login_enabled') === 'on' : false,
            'free_shipping_enabled' => $this->has('free_shipping_enabled') ? $this->get('free_shipping_enabled') === 'on' : false,
            'novaPoshta_enabled' => $this->has('novaPoshta_enabled') ? $this->get('novaPoshta_enabled') === 'on' : false,
            'local_pickup_enabled' => $this->has('local_pickup_enabled') ? $this->get('local_pickup_enabled') === 'on' : false,
            'cod_enabled' => $this->has('cod_enabled') ? $this->get('cod_enabled') === 'on' : false,
            'bank_transfer_enabled' => $this->has('bank_transfer_enabled') ? $this->get('bank_transfer_enabled') === 'on' : false,
            'check_payment_enabled' => $this->has('check_payment_enabled') ? $this->get('check_payment_enabled') === 'on' : false,
            'flat_rate_enabled' => $this->has('flat_rate_enabled') ? $this->get('flat_rate_enabled') === 'on' : false,
        ]);
    }

    /**
     * Returns currency rate refresh frequencies..
     *
     * @return array
     */
    private function refreshFrequencies()
    {
        return array_keys(trans('setting::settings.form.auto_refresh_currency_rate_frequencies'));
    }


    /**
     * Returns order statuses.
     *
     * @return array
     */
    private function orderStatuses()
    {
        return array_keys(trans('order::statuses'));
    }


    /**
     * Returns mail encryption protocols.
     *
     * @return array
     */
    private function mailEncryptionProtocols()
    {
        return array_keys(trans('setting::settings.form.mail_encryption_protocols'));
    }
}
