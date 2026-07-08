<?php

return [
    'email_template' => 'Шаблон письма',
    'email_templates' => 'Шаблоны писем',

    'tabs' => [
        'group' => [
            'email_template_information' => 'Информация о шаблоне',
        ],
        'general' => 'Общее',
        'content' => 'Содержимое',
    ],

    'types' => [
        'new_order' => 'Новый заказ',
        'order_status' => 'Статусы заказов',
        'return_status' => 'Статусы возвратов',
        'customer_registration' => 'Регистрация клиента',
        'customer_activation' => 'Активация клиента',
        'customer_password_reset' => 'Восстановление пароля клиента',
        'gift_certificate' => 'Подарочный сертификат',
        'transaction' => 'Транзакции',
        'review' => 'Отзыв',
        'contact_form' => 'Форма связи',
        'customer_question_answer' => 'Вопрос / ответ клиента',
    ],

    'recipients' => [
        'customer' => 'Клиент',
        'admin' => 'Администратор',
    ],

    'form' => [
        'enable_the_email_template' => 'Включить отправку этого письма',
        'show_product_image' => 'Показывать фото товара в письме',
        'available_shortcodes' => 'Доступные шорткоды',
        'no_shortcodes' => 'Для выбранного типа письма нет доступных шорткодов',
        'any_status' => 'Любой статус',
        'test_email' => 'Тестовая отправка',
        'test_email_address' => 'Email для теста',
        'test_email_placeholder' => 'example@email.com',
        'test_email_help' => 'Адрес используется только для тестовой отправки и нигде не сохраняется.',
        'send_test_email' => 'Отправить тестовое письмо',
        'sending_test_email' => 'Отправляем...',
        'test_email_sent' => 'Тестовое письмо отправлено.',
        'test_email_failed' => 'Не удалось отправить тестовое письмо.',
        'no_products_for_test_email' => 'Для тестовой отправки не найдено активных товаров.',
    ],

    'filters' => [
        'all' => 'Все',
        'status' => 'Статус',
        'active' => 'Активные',
        'inactive' => 'Неактивные',
    ],

    'mail' => [
        'image' => 'Фото',
        'product' => 'Товар',
        'sku' => 'Артикул',
        'stock' => 'Остаток',
        'qty' => 'Кол-во',
        'total' => 'Сумма',
        'price' => 'Цена',
        'packaging' => 'Упаковка',
        'pieces' => 'шт.',
        'gift' => 'Подарок',
    ],
    'demo' => [
        'firstname' => 'Иван',
        'lastname' => 'Петров',
        'fullname' => 'Иван Петров',
        'email' => 'customer@example.com',
        'phone' => '+38 (099) 123-45-67',

        'address_1' => 'Отделение Новой почты №1',
        'address_2' => 'ул. Центральная, 10',
        'city' => 'Киев',
        'zip' => '01001',
        'country_code' => 'UA',

        'shipping_method' => 'Новая почта',
        'payment_method' => 'Оплата картой VISA/MasterCard онлайн',

        'message' => 'Это тестовое сообщение из формы обратной связи.',
        'question' => 'Подойдут ли эти линзы для чувствительных глаз?',
        'answer' => 'Да, этот товар подходит для чувствительных глаз, но рекомендуем проконсультироваться со специалистом.',

        'return_id' => 'RET-10001',
        'return_status' => 'Ожидает обработки',
        'return_reason' => 'Не подошёл товар',
        'return_comment' => 'Клиент просит заменить товар на другой вариант.',

        'gift_certificate_code' => 'GIFT-DEMO-100',
        'gift_certificate_amount' => '1000 грн',
        'gift_certificate_from' => 'Иван Петров',
        'gift_certificate_message' => 'Поздравляю! Это тестовый подарочный сертификат.',

        'transaction_id' => 'TX-DEMO-10001',
        'transaction_amount' => '2500 грн',

        'product_name' => 'Тестовый товар',
        'option_field_value' => 'Тестовое значение опции',
    ],
];
