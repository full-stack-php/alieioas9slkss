<?php

namespace Modules\SeoFilter\Http\Controllers\Admin;

use Modules\Admin\Traits\HasCrudActions;
use Modules\Category\Entities\Category;
use Modules\SeoFilter\Entities\SeoFilter;
use Modules\SeoFilter\Http\Requests\SaveSeoFilterRequest;

class SeoFilterController
{
    use HasCrudActions;

    protected $model = SeoFilter::class;

    protected $label = 'seo_filter::seo_filters.seo_filter';

    protected $viewPath = 'seo_filter::admin.seo_filters';

    protected $validation = SaveSeoFilterRequest::class;

    protected $with = ['category'];

    protected function formData(): array
    {
        return [
            'categories' => Category::treeList(),
        ];
    }
}
