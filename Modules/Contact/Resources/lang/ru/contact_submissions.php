<?php

return [
    'contact_submissions' => 'Заявки с форм',
    'contact_submission' => 'Заявка с формы',

    'types' => [
        'callback' => 'Заявка на звонок',
        'contact' => 'Форма контактов',
        'unknown' => 'Неизвестно',
    ],

    'short_types' => [
        'callback' => 'Звонок',
        'contact' => 'Контакты',
    ],

    'filters' => [
        'all_types' => 'Все типы',
        'all_read_statuses' => 'Все по прочтению',
        'all_processed_statuses' => 'Все по обработке',
        'new' => 'Новые',
        'read' => 'Прочитанные',
        'processed' => 'Обработанные',
        'unprocessed' => 'Необработанные',
    ],

    'table' => [
        'type' => 'Тип',
        'customer' => 'Клиент',
        'contacts' => 'Контакты',
        'source_url' => 'Страница',
        'read_status' => 'Прочтение',
        'processed_status' => 'Обработка',
    ],

    'show' => [
        'information' => 'Информация о заявке',
        'back_to_list' => 'Назад к списку',
        'mark_as_processed' => 'Отметить как обработанную',
        'mark_as_unprocessed' => 'Вернуть в необработанные',
    ],

    'fields' => [
        'id' => 'ID',
        'type' => 'Тип',
        'name' => 'Имя',
        'phone' => 'Телефон',
        'email' => 'Email',
        'topic' => 'Тема',
        'comment' => 'Комментарий',
        'preferred_call_at' => 'Желаемое время звонка',
        'source_url' => 'Страница отправки',
        'ip_address' => 'IP',
        'user_agent' => 'User Agent',
        'created_at' => 'Создано',
        'read_at' => 'Прочитано',
        'processed_at' => 'Обработано',
        'processed_by' => 'Обработал',
    ],

    'statuses' => [
        'new' => 'Новое',
        'read' => 'Прочитано',
        'processed' => 'Обработано',
        'unprocessed' => 'Не обработано',
    ],

    'messages' => [
        'marked_as_processed' => 'Заявка отмечена как обработанная.',
        'marked_as_unprocessed' => 'Заявка отмечена как необработанная.',
    ],

    'mail' => [
        'new_callback_subject' => 'Новая заявка на обратный звонок',
        'new_contact_subject' => 'Новая заявка со страницы контактов',
    ],

    'buttons' => [
        'open_page' => 'Открыть страницу',
    ],

    'empty' => '—',
];
