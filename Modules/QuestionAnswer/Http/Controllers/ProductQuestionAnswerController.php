<?php

namespace Modules\QuestionAnswer\Http\Controllers;

use Illuminate\Http\Response;
use Modules\QuestionAnswer\Entities\QuestionAnswer;
use Modules\Product\Entities\Product;
use Modules\QuestionAnswer\Events\QuestionAnswerSubmitted;
use Modules\QuestionAnswer\Http\Requests\StoreQuestionAnswerRequest;

class ProductQuestionAnswerController
{
    /**
     * Display a listing of the resource.
     *
     * @param int $productId
     *
     * @return Response
     */
    public function index($productId)
    {
        return QuestionAnswer::where('product_id', $productId)->latest()->paginate(5);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param int $productId
     * @param StoreQuestionAnswerRequest $request
     *
     * @return Response
     */
    public function store($productId, StoreQuestionAnswerRequest $request)
    {

        try {
            $questionAnswer = Product::findOrFail($productId)
                ->questionsanswers()
                ->create([
                    'asker_id' => auth()->id(),
                    'asker_name' => $request->asker_name,
                    'asker_phone' => $request->asker_phone,
                    'question' => $request->question,
                    'is_approved' => 0,
                ]);

            event(new QuestionAnswerSubmitted($questionAnswer));

            return response()->json([
                'success' => true,
                'message' => trans('review::messages.submitted_for_approval')
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при сохранении вопроса ответа: ' . $e->getMessage());

            return response()->json([
                'error' => 'Произошла ошибка при сохранении вопроса ответа. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
}
