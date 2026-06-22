<?php

namespace Modules\QuestionAnswer\Http\Controllers;

use Illuminate\Http\Response;

class QuestionAnswerProductController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return auth()->user()
            ->questionsanswers()
            ->withoutGlobalScope('approved')
            ->with('product.files')
            ->whereHas('product')
            ->latest()
            ->paginate(10);
    }
}
