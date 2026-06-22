<?php

namespace Modules\Menu\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;

class SaveMenuItemRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'menu::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => ['required', Rule::in('blog_category','category', 'page', 'url')],
            'blog_category_id' => 'required_if:type,blog_category',
            'category_id' => 'required_if:type,category',
            'page_id' => 'required_if:type,page',
            'url' => 'required_if:type,url',
            'target' => ['required', Rule::in('_self', '_blank')],
            'is_fluid' => 'required',
            'is_active' => 'required',
        ];
        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_fluid' => $this->has('is_fluid') ? $this->get('is_fluid') === 'on' : false,
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
        ]);
    }
}
