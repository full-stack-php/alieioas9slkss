<?php

namespace Modules\Redirect\Http\Requests;

use Modules\Core\Http\Requests\Request;

class ImportRedirectsRequest extends Request
{
    protected $availableAttributes = 'redirect::attributes';

    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv,txt',
            ],
        ];
    }
}
