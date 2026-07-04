<?php

namespace Modules\Redirect\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\UrlResolver;
use Modules\Core\Http\Requests\Request;
use Modules\Redirect\Entities\Redirect;
use Modules\Redirect\Services\RedirectUrl;

class SaveRedirectRequest extends Request
{
    protected $availableAttributes = 'redirect::attributes';

    public function rules()
    {
        return [
            'old_url' => [
                'required',
                'max:255',
                Rule::unique('redirects', 'old_url')->ignore($this->route('id')),
            ],
            'new_url' => [
                'required',
                'max:2048',
            ],
            'status_code' => [
                'required',
                Rule::in([301, 302]),
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'comment' => [
                'nullable',
                'max:5000',
            ],
            'force_save' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $statusCode = (int) $this->get('status_code');

        $this->merge([
            'old_url' => RedirectUrl::normalizeOldUrl($this->get('old_url')),
            'new_url' => RedirectUrl::normalizeNewUrl($this->get('new_url')),
            'status_code' => in_array($statusCode, [301, 302], true) ? $statusCode : 301,
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
            'force_save' => $this->has('force_save') ? $this->get('force_save') === 'on' : false,
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = $this->route('id') ? (int) $this->route('id') : null;

            if (!RedirectUrl::isValid($this->get('old_url'))) {
                $validator->errors()->add('old_url', trans('redirect::redirects.validation.invalid_old_url'));
            }

            if (!RedirectUrl::isValid($this->get('new_url'))) {
                $validator->errors()->add('new_url', trans('redirect::redirects.validation.invalid_new_url'));
            }

            if (RedirectUrl::normalizeOldUrl($this->get('old_url')) === RedirectUrl::normalizeOldUrl($this->get('new_url'))) {
                $validator->errors()->add('new_url', trans('redirect::redirects.validation.same_url'));
            }

            if (Redirect::hasChain($this->get('new_url'), $id)) {
                $validator->errors()->add('new_url', trans('redirect::redirects.validation.chain'));
            }

            if (Redirect::hasCycle($this->get('old_url'), $this->get('new_url'), $id)) {
                $validator->errors()->add('new_url', trans('redirect::redirects.validation.cycle'));
            }

            if (!$this->boolean('force_save') && $this->oldUrlExistsAsActivePage()) {
                $validator->errors()->add('old_url', trans('redirect::redirects.validation.active_page_exists'));
            }
        });
    }

    private function oldUrlExistsAsActivePage(): bool
    {
        $resolver = app(UrlResolver::class);

        return !is_null(
            $resolver->resolve(RedirectUrl::normalizeOldUrl($this->get('old_url')))
        );
    }
}
