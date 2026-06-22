<?php

namespace Modules\Brand\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Brand\Entities\Brand;
use Modules\Core\Http\Requests\Request;

class SaveBrandRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'brand::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'slug' => $this->getSlugRules(),
            'is_active' => 'required',
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["{$locale}.h1_name"] = 'required';
            $rules["{$locale}.description"] = 'required';
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


    private function getSlugRules()
    {
        $rules = $this->route()->getName() === 'admin.brands.update'
            ? ['required']
            : ['sometimes'];

        $slug = Brand::withoutGlobalScope('active')->where('id', $this->id)->value('slug');

        $rules[] = Rule::unique('brands', 'slug')->ignore($slug, 'slug');

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        foreach (supported_locales() as $locale => $language) {
            $attributes["{$locale}.name"] = "name";
            $attributes["{$locale}.h1_name"] = "H1 tag";
            $attributes["{$locale}.description"] = "description";
        }

        return $attributes;
    }
}
