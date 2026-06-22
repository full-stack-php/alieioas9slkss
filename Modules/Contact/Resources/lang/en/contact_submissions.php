<?php

return [
    'contact_submissions' => 'Contact Submissions',
    'contact_submission' => 'Contact Submission',

    'types' => [
        'callback' => 'Callback Request',
        'contact' => 'Contact Form',
        'unknown' => 'Unknown',
    ],

    'short_types' => [
        'callback' => 'Callback',
        'contact' => 'Contacts',
    ],

    'filters' => [
        'all_types' => 'All Types',
        'all_read_statuses' => 'All Read Statuses',
        'all_processed_statuses' => 'All Processed Statuses',
        'new' => 'New',
        'read' => 'Read',
        'processed' => 'Processed',
        'unprocessed' => 'Unprocessed',
    ],

    'table' => [
        'type' => 'Type',
        'customer' => 'Customer',
        'contacts' => 'Contacts',
        'source_url' => 'Source Page',
        'read_status' => 'Read Status',
        'processed_status' => 'Processing Status',
    ],

    'show' => [
        'information' => 'Submission Information',
        'back_to_list' => 'Back to List',
        'mark_as_processed' => 'Mark as Processed',
        'mark_as_unprocessed' => 'Mark as Unprocessed',
    ],

    'fields' => [
        'id' => 'ID',
        'type' => 'Type',
        'name' => 'Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'topic' => 'Topic',
        'comment' => 'Comment',
        'preferred_call_at' => 'Preferred Callback Time',
        'source_url' => 'Source Page',
        'ip_address' => 'IP',
        'user_agent' => 'User Agent',
        'created_at' => 'Created At',
        'read_at' => 'Read At',
        'processed_at' => 'Processed At',
        'processed_by' => 'Processed By',
    ],

    'statuses' => [
        'new' => 'New',
        'read' => 'Read',
        'processed' => 'Processed',
        'unprocessed' => 'Unprocessed',
    ],

    'messages' => [
        'marked_as_processed' => 'Submission has been marked as processed.',
        'marked_as_unprocessed' => 'Submission has been marked as unprocessed.',
    ],

    'mail' => [
        'new_callback_subject' => 'New callback request',
        'new_contact_subject' => 'New contact form submission',
    ],

    'buttons' => [
        'open_page' => 'Open Page',
    ],

    'empty' => '—',
];
