<?php

namespace Modules\Contact\Http\Requests;

use Modules\Core\Http\Requests\Request;
use Modules\Support\Rules\GoogleRecaptcha;

class StoreContactSubmissionRequest extends Request
{
    protected $availableAttributes = 'contact::attributes';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],

            'source_url' => ['nullable', 'string'],

            'g-recaptcha-response' => [
                'bail',
                'sometimes',
                'required',
                new GoogleRecaptcha(),
            ],
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'g-recaptcha-response.required' => trans('support::recaptcha.validation.failed_to_verify'),
        ]);
    }
}
