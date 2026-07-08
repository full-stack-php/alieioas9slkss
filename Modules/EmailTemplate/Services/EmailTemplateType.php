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
    public const CUSTOMER_QUESTION_ANSWER = 'customer_question_answer';
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
            self::CUSTOMER_QUESTION_ANSWER => trans('emailtemplate::email_templates.types.customer_question_answer'),
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
        return array_values(array_unique(array_merge(...array_values(static::shortcodesByType()))));
    }

    public static function shortcodesByType(): array
    {
        return [
            self::NEW_ORDER => array_merge(
                static::customerShortcodes(),
                static::orderShortcodes(),
                static::storeShortcodes()
            ),

            self::ORDER_STATUS => array_merge(
                static::customerShortcodes(),
                static::orderShortcodes(),
                static::storeShortcodes()
            ),

            self::RETURN_STATUS => array_merge(
                static::customerShortcodes(),
                [
                    '{$return_id}',
                    '{$return_status}',
                    '{$return_reason}',
                    '{$return_comment}',
                ],
                static::storeShortcodes()
            ),

            self::CUSTOMER_REGISTRATION => array_merge(
                static::customerShortcodes(),
                static::storeShortcodes()
            ),

            self::CUSTOMER_ACTIVATION => array_merge(
                static::customerShortcodes(),
                [
                    '{$activation_url}',
                ],
                static::storeShortcodes()
            ),

            self::CUSTOMER_PASSWORD_RESET => array_merge(
                static::customerShortcodes(),
                [
                    '{$reset_url}',
                ],
                static::storeShortcodes()
            ),

            self::GIFT_CERTIFICATE => array_merge(
                static::customerShortcodes(),
                [
                    '{$gift_certificate_code}',
                    '{$gift_certificate_amount}',
                    '{$gift_certificate_from}',
                    '{$gift_certificate_message}',
                ],
                static::storeShortcodes()
            ),

            self::TRANSACTION => array_merge(
                static::customerShortcodes(),
                [
                    '{$transaction_id}',
                    '{$transaction_amount}',
                    '{$payment_method}',
                ],
                static::storeShortcodes()
            ),

            self::REVIEW => array_merge(
                static::customerShortcodes(),
                [
                    '{$review_url}',
                    '{$product_name}',
                    '{$product_url}',
                    '{$review_rating}',
                    '{$review_plus}',
                    '{$review_minus}',
                    '{$review_comment}',
                ],
                static::storeShortcodes()
            ),

            self::CUSTOMER_QUESTION_ANSWER => array_merge(
                static::customerShortcodes(),
                [
                    '{$question}',
                    '{$answer}',
                    '{$product_name}',
                    '{$product_url}',
                ],
                static::storeShortcodes()
            ),

            self::CONTACT_FORM => array_merge(
                [
                    '{$fullname}',
                    '{$email}',
                    '{$phone}',
                    '{$message}',
                ],
                static::storeShortcodes()
            ),
        ];
    }

    private static function customerShortcodes(): array
    {
        return [
            '{$firstname}',
            '{$lastname}',
            '{$fullname}',
            '{$email}',
            '{$phone}',
        ];
    }

    private static function orderShortcodes(): array
    {
        return [
            '{$order_id}',
            '{$order_date}',
            '{$order_status}',
            '{$order_total}',
            '{$order_subtotal}',
            '{$order_discount}',
            '{$shipping_cost}',
            '{$shipping_method}',
            '{$payment_method}',
            '{$billing_address}',
            '{$shipping_address}',
            '{$cart_weight}',
            '{$order_products}',
        ];
    }

    private static function storeShortcodes(): array
    {
        return [
            '{$store_name}',
            '{$store_email}',
            '{$store_phone}',
            '{$store_address}',
            '{$site_url}',
        ];
    }

}
