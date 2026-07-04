<?php

namespace Modules\Redirect\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Redirect\Entities\Redirect;
use Modules\Redirect\Exports\RedirectsExport;
use Modules\Redirect\Imports\RedirectsImport;
use Modules\Redirect\Http\Requests\SaveRedirectRequest;
use Modules\Redirect\Http\Requests\ImportRedirectsRequest;

class RedirectController
{
    use HasCrudActions;

    protected $model = Redirect::class;

    protected $label = 'redirect::redirects.redirect';

    protected $viewPath = 'redirect::admin.redirects';

    protected $validation = SaveRedirectRequest::class;

    public function import(ImportRedirectsRequest $request)
    {
        $result = (new RedirectsImport())->handle(
            $request->file('file')->getRealPath()
        );

        if ($result['has_critical_errors']) {
            return redirect()
                ->route('admin.redirects.index')
                ->withErrors($result['errors']);
        }

        return redirect()
            ->route('admin.redirects.index')
            ->withSuccess(trans('redirect::redirects.import.success', [
                'created' => $result['created'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
            ]));
    }

    public function export(Request $request)
    {
        return (new RedirectsExport())->download(
            $request->only(['format', 'status', 'status_code', 'page_type'])
        );
    }
}
