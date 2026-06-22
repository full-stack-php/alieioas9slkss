<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Blog\Entities\BlogCategory;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Blog\Http\Requests\SaveBlogCategoryRequest;

class BlogCategoryController extends Controller
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = BlogCategory::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'blog::blog.categories.name';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'blog::admin.categories';

    /**
     * Form requests for the resource.
     *
     * @var array
     */
    protected $validation = SaveBlogCategoryRequest::class;

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

        if (method_exists(BlogCategory::class, 'translations')) {
            $query->with(['translations' => function ($translationQuery) {
                $translationQuery->withoutGlobalScope('locale');
            }]);
        }

        $entity = $query->findOrFail($id);

        $data = $entity->toArray();
        $data['translations_data'] = $entity->translations->toArray();
        $data['meta_data'] = isset($entity->meta->translations) ? $entity->meta->translations->toArray() : null;
        return $data;

//        $entity = $this->getEntity($id);
//        dd($entity);
//        $data = $entity->toArray();
//        $data['translations_data'] = $entity->translations->toArray();
//        $data['meta_data'] = $entity->meta->translations->toArray();
//        return $data;

//        return BlogCategory::with('files')->withoutGlobalScope('active')->withoutGlobalScope('locale')->find($id);
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
        BlogCategory::withoutGlobalScope('active')
            ->findOrFail($ids)
            ->delete();

        return back()->withSuccess(trans('admin::messages.resource_deleted', ['resource' => $this->getLabel()]));
    }
}
