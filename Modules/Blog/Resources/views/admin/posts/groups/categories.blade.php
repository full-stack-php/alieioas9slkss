<div class="tab-pane fade" id="vl-pills-categories" role="tabpanel" aria-labelledby="vl-pills-categories-tab">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ trans('blog::blog.posts.groups.categories') }}</h4>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="blog_category_id" class="form-label control-label text-left">
                    {{ trans('blog::attributes.posts.category') }}
                </label>

                <select name="blog_category_id" id="blog_category_id" class="form-control custom-select-black" x-model="form.blog_category_id">

                </select>

                <template x-if="errors.has('blog_category_id')">
                    <span class="help-block text-red" x-text="errors.get('blog_category_id')"></span>
                </template>
            </div>
        </div>
    </div>
</div>
