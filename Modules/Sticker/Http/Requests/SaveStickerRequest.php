<?php

namespace Modules\Sticker\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Sticker\Entities\Sticker;

class SaveStickerRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'sticker::attributes';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => [
                'required',
                Rule::in(Sticker::TYPES),
            ],

            'text_color' => [
                'required_if:type,' . Sticker::TYPE_LABEL,
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'background_color' => [
                'required_if:type,' . Sticker::TYPE_LABEL,
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'image_background_color' => [
                'required_if:type,' . Sticker::TYPE_INFO,
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'files' => [
                'nullable',
                'array',
            ],

            'files.image' => [
                'required_if:type,'
                . Sticker::TYPE_IMAGE
                . ','
                . Sticker::TYPE_INFO,
                'nullable',
                'integer',
                Rule::exists('files', 'id'),
            ],
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = [
                'required',
                'string',
                'max:255',
            ];

            $rules["{$locale}.image_alt"] = [
                'required_if:type,'
                . Sticker::TYPE_IMAGE
                . ','
                . Sticker::TYPE_INFO,
                'nullable',
                'string',
                'max:255',
            ];

            $rules["{$locale}.description"] = [
                'required_if:type,' . Sticker::TYPE_INFO,
                'nullable',
                'string',
            ];

            $rules["{$locale}.popup_description"] = [
                'required_if:type,' . Sticker::TYPE_INFO,
                'nullable',
                'string',
            ];
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
            'is_active' => $this->has('is_active')
                ? $this->get('is_active') === 'on'
                : false,

            'sort_order' => $this->filled('sort_order')
                ? $this->get('sort_order')
                : 0,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        foreach (supported_locales() as $locale => $language) {
            $languageName = $language['name'];

            $attributes["{$locale}.name"] =
                trans('sticker::attributes.name')
                . " ({$languageName})";

            $attributes["{$locale}.image_alt"] =
                trans('sticker::attributes.image_alt')
                . " ({$languageName})";

            $attributes["{$locale}.description"] =
                trans('sticker::attributes.description')
                . " ({$languageName})";

            $attributes["{$locale}.popup_description"] =
                trans('sticker::attributes.popup_description')
                . " ({$languageName})";
        }

        return $attributes;
    }
}
