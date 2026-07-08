<?php

namespace Modules\EmailTemplate\Services;

class EmailTemplateType
{
    public const NEW_ORDER = 'new_order';
    public const ORDER_STATUS = 'order_status';
    public const RETURN_STATUS = 'return_status';
    public const CUSTOMER_REGISTRATION = 'customer_registration';
    public const CUSTOMER_ACTIVATION = 'customer_activation';
    public const CUSTOMER_PASSWORD_RESET = 'customer_password_reset';
    public const GIFT_CERTIFICATE = 'gift_certificate';
    public const TRANSACTION = 'transaction';
    public const REVIEW = 'review';
    public const CONTACT_FORM = 'contact_form';

    public const RECIPIENT_CUSTOMER = 'customer';
    public const RECIPIENT_ADMIN = 'admin';

    public static function all(): array
    {
        return [
            self::NEW_ORDER => trans('emailtemplate::email_templates.types.new_order'),
            self::ORDER_STATUS => trans('emailtemplate::email_templates.types.order_status'),
            self::RETURN_STATUS => trans('emailtemplate::email_templates.types.return_status'),
            self::CUSTOMER_REGISTRATION => trans('emailtemplate::email_templates.types.customer_registration'),
            self::CUSTOMER_ACTIVATION => trans('emailtemplate::email_templates.types.customer_activation'),
            self::CUSTOMER_PASSWORD_RESET => trans('emailtemplate::email_templates.types.customer_password_reset'),
            self::GIFT_CERTIFICATE => trans('emailtemplate::email_templates.types.gift_certificate'),
            self::TRANSACTION => trans('emailtemplate::email_templates.types.transaction'),
            self::REVIEW => trans('emailtemplate::email_templates.types.review'),
            self::CONTACT_FORM => trans('emailtemplate::email_templates.types.contact_form'),
        ];
    }

    public static function recipients(): array
    {
        return [
            self::RECIPIENT_CUSTOMER => trans('emailtemplate::email_templates.recipients.customer'),
            self::RECIPIENT_ADMIN => trans('emailtemplate::email_templates.recipients.admin'),
        ];
    }

    public static function label(?string $type): string
    {
        return static::all()[$type] ?? (string) $type;
    }

    public static function recipientLabel(?string $recipient): string
    {
        return static::recipients()[$recipient] ?? (string) $recipient;
    }

    public static function shortcodes(): array
    {
        return [
            '{$firstname}',
            '{$lastname}',
            '{$fullname}',
            '{$email}',
            '{$phone}',
            '{$order_id}',
            '{$order_status}',
            '{$order_total}',
            '{$order_subtotal}',
            '{$order_discount}',
            '{$shipping_method}',
            '{$payment_method}',
            '{$billing_address}',
            '{$shipping_address}',
            '{$cart_weight}',
            '{$order_products}',
            '{$reset_url}',
            '{$store_name}',
            '{$store_email}',
            '{$site_url}',
            '{$order_date}',
            '{$shipping_cost}',
            '{$store_phone}',
            '{$store_address}',
        ];
    }

}
