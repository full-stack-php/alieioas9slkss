<?php

namespace Modules\Order\Http\Requests;

use Modules\Core\Http\Requests\Request;

class SaveOrderStatusRequest extends Request
{
    /**
     * Array of available attributes for custom localization names mapping.
     *
     * @var string
     */
    protected $availableLocales = 'order::statuses.attributes';

    /**
     * Get the validation rules that apply to the base global fields.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'color' => ['required', 'string', 'max:7', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'is_active' => ['required', 'boolean'],
        ];


        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
        }
        return $rules;
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
        ]);
    }

    /**
     * Get the validation rules that apply to the localized translatable fields.
     *
     * @return array
     */
    public function localeRules()
    {
        return [
            'name' => ['required', 'string', 'max:191'],
        ];
    }
}
