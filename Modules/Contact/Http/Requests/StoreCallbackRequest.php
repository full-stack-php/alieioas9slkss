<?php

namespace Modules\Contact\Http\Requests;

use Modules\Core\Http\Requests\Request;

class StoreCallbackRequest extends Request
{
    protected $availableAttributes = 'contact::attributes';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],

            'email_buyer' => ['nullable', 'email', 'max:255'],
            'comment_buyer' => ['nullable', 'string'],

            'topic_callback_send' => ['nullable', 'string', 'max:255'],
            'time_callback_on' => ['nullable', 'string'],

            'url_site' => ['nullable', 'string'],
        ];
    }
}
