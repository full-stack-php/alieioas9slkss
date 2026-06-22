<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Page\Entities\Page;
use Modules\Core\Http\Requests\Request;

class SavePageRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var array
     */
    protected $availableAttributes = 'page::attributes';


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
            $rules["{$locale}.body"] = 'required';
        }

        return $rules;
    }


    private function getSlugRules()
    {
        $rules = $this->route()->getName() === 'admin.pages.update'
            ? ['required']
            : ['sometimes'];

        $slug = Page::withoutGlobalScope('active')->where('id', $this->id)->value('slug');

        $rules[] = Rule::unique('pages', 'slug')->ignore($slug, 'slug');

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

    public function attributes()
    {
        $attributes = [];
        foreach (supported_locales() as $locale => $language) {
            $attributes["{$locale}.name"] = "name";

            $attributes["{$locale}.h1_name"] = "H1 tag";
            $attributes["{$locale}.body"] = "description";
        }

        return $attributes;
    }

}
