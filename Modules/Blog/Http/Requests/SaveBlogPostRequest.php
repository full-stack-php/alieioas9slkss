<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Blog\Entities\BlogPost;
use Modules\Core\Http\Requests\Request;

class SaveBlogPostRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'blog::attributes.blogs.blogs';


    public function rules()
    {

        $rules = [
            'slug' => $this->getSlugRule(),
            'is_active' => 'required',
            'blog_category_id' => 'required',
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["{$locale}.h1_name"] = 'required';
            $rules["{$locale}.description"] = 'required';
        }

        return $rules;
    }


    private function getSlugRule(): array
    {
        $rules = $this->route()->getName() === 'admin.blog_posts.update' ? ['required'] : ['sometimes'];

        $slug = BlogPost::withoutGlobalScope('active')->where('id', $this->id)->value('slug');

        $rules[] = Rule::unique('blog_posts', 'slug')->ignore($slug, 'slug');

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
            $attributes["{$locale}.description"] = "description";
        }

        return $attributes;
    }
}
