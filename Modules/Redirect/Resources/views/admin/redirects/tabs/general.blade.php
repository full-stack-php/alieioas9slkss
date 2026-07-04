<div class="row">
    <div class="col-md-12">
        {{ Form::text('old_url', trans('redirect::attributes.old_url'), $errors, $redirect, ['required' => true]) }}

        {{ Form::text('new_url', trans('redirect::attributes.new_url'), $errors, $redirect, ['required' => true]) }}

        {{ Form::select('status_code', trans('redirect::attributes.status_code'), $errors, trans('redirect::redirects.form.status_codes'), $redirect) }}

        {{ Form::textarea('comment', trans('redirect::attributes.comment'), $errors, $redirect, ['rows' => 4]) }}

        {{ Form::checkbox('is_active', trans('redirect::attributes.is_active'), trans('redirect::redirects.form.enable_the_redirect'), $errors, $redirect) }}

        {{ Form::checkbox('force_save', trans('redirect::attributes.force_save'), trans('redirect::redirects.form.force_save'), $errors, (object) ['force_save' => false]) }}
    </div>
</div>
