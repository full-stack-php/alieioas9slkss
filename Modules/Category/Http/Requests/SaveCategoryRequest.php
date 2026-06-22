<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Requests\Request;

class SaveCategoryRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'category::attributes';


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
//            $rules["{$locale}.description"] = 'required';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
            'is_searchable' => $this->has('is_searchable') ? $this->get('is_searchable') === 'on' : false,
        ]);
    }

    public function attributes()
    {
        $attributes = [];
        foreach (supported_locales() as $locale => $language) {
            $attributes["{$locale}.name"] = "name";

            $attributes["{$locale}.h1_name"] = "H1 tag";
//            $attributes["{$locale}.body"] = "description";
        }

        return $attributes;
    }


    private function getSlugRules()
    {
        $rules = $this->route()->getName() === 'admin.categories.update'
            ? ['required']
            : ['nullable'];

        $slug = Category::withoutGlobalScope('active')->where('id', $this->id)->value('slug');

        $rules[] = Rule::unique('categories', 'slug')->ignore($slug, 'slug');

        return $rules;
    }
}
