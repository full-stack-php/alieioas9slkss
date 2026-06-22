<?php

namespace Modules\Slider\Http\Requests;

use Modules\Core\Http\Requests\Request;

class SaveSliderRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'slider::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        foreach (supported_locales() as $locale => $language) {
            $rules["slides.*.{$locale}.title"] = 'required|string|max:255';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'autoplay' => $this->has('autoplay') ? $this->get('autoplay') === 'on' : false,
        ]);
    }
}
