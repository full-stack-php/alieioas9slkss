<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Blog\Entities\BlogCategory;

class SaveBlogCategoryRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'blog::attributes.blogs.categories';


    public function rules()
    {
        $rules = [
            'slug' => $this->getSlugRule(),
            'is_active' => 'required',
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["{$locale}.h1_name"] = 'required';
//            $rules["{$locale}.description"] = 'required';
        }

        return $rules;
    }


    private function getSlugRule(): array
    {

        $rules = $this->route()->getName() === 'admin.blog_categories.update' ? ['required'] : ['sometimes'];

        $slug = BlogCategory::withoutGlobalScope('active')->where('id', $this->id)
            ->value('slug');

        $rules[] = Rule::unique('blog_categories', 'slug')->ignore($slug, 'slug');

        return $rules;
    }

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
//            $attributes["{$locale}.body"] = "description";
        }

        return $attributes;
    }

}
