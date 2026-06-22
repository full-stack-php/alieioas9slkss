<?php

namespace Modules\Category\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Category\Entities\Category;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Category\Http\Requests\SaveCategoryRequest;

class CategoryController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'category::categories.category';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'category::admin.categories';

    /**
     * Form requests for the resource.
     *
     * @var array|string
     */
    protected $validation = SaveCategoryRequest::class;


    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {

        $query = $this->getModel()
            ->with('files')
            ->withoutGlobalScope('active');

        if (method_exists(Category::class, 'translations')) {
            $query->with(['translations' => function ($translationQuery) {
                $translationQuery->withoutGlobalScope('locale');
            }]);
        }
        $entity = $query->findOrFail($id);



        $data = $entity->toArray();
        $data['translations_data'] = $entity->translations->toArray();
        $data['meta_data'] = isset($entity->meta->translations) ? $entity->meta->translations->toArray() : null;
        $data['data_faq'] = isset($entity->faqs) ? $entity->faqs->toArray() : null;

        return $data;
    }


    /**
     * Destroy resources by given ids.
     *
     * @param string $ids
     *
     * @return Response
     */
    public function destroy(string $ids)
    {
        Category::withoutGlobalScope('active')
            ->findOrFail($ids)
            ->delete();

        return back()->withSuccess(trans('admin::messages.resource_deleted', ['resource' => $this->getLabel()]));
    }
}
