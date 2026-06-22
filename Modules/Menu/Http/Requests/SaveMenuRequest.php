<?php

namespace Modules\Menu\Http\Requests;

use Modules\Core\Http\Requests\Request;

class SaveMenuRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var array
     */
    protected $availableAttributes = 'menu::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
        ]);
    }
}
