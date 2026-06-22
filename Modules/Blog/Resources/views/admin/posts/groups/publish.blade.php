<div class="tab-pane fade" id="vl-pills-publish" role="tabpanel" aria-labelledby="vl-pills-publish-tab">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ trans('blog::blog.posts.groups.publish') }}</h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="publish_status" class="control-label text-left asterisk-align">
                    {{ trans('blog::attributes.posts.publish_status') }}

                    <span class="text-red">*</span>
                </label>

                <select name="publish_status" id="publish_status" class="form-control custom-select-black" x-model="form.publish_status">
                    <option value="published" {{ old('publish_status') == 'published' ? 'selected' : '' }}>
                        {{ trans('blog::blog.posts.form.publish_status.published') }}
                    </option>

                    <option value="unpublished" {{ old('publish_status') == 'unpublished' ? 'selected' : '' }}>
                        {{ trans('blog::blog.posts.form.publish_status.unpublished') }}
                    </option>
                </select>

                <template x-if="errors.has('publish_status')">
                    <span class="help-block text-red" x-text="errors.get('publish_status')"></span>
                </template>
            </div>
        </div>
    </div>
</div>
