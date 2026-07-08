<?php

namespace Modules\QuestionAnswer\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\QuestionAnswer\Entities\QuestionAnswer;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\QuestionAnswer\Events\QuestionAnswerAnswered;
use Modules\QuestionAnswer\Http\Requests\UpdateQuestionAnswerRequest;

class QuestionAnswerController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = QuestionAnswer::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'questionanswer::questions_answers.questions_answers';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'questionanswer::admin';

    /**
     * Form requests for the resource.
     *
     * @var array|string
     */
    protected $validation = UpdateQuestionAnswerRequest::class;


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $questionanswer = QuestionAnswer::withoutGlobalScope('approved')->findOrFail($id);

        $tabs = TabManager::get('questions_answers');

        return view('questionanswer::admin.edit', compact('questionanswer', 'tabs'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id, UpdateQuestionAnswerRequest $request)
    {
        $questionanswer = QuestionAnswer::withoutGlobalScope('approved')->findOrFail($id);

        $oldAnswer = $questionanswer->answer;
        $wasApproved = $questionanswer->is_approved;

        $questionanswer->update($request->except(
            array_merge(array_keys(request()->query()), ['search_terms'])
        ));

        if (
            $questionanswer->is_approved
            && !empty($questionanswer->answer)
            && (!$wasApproved || $oldAnswer !== $questionanswer->answer)
        ) {
            event(new QuestionAnswerAnswered($questionanswer));
        }

        return redirect()->route('admin.questions_answers.index');
    }


    /**
     * Destroy resources by given ids.
     *
     * @param string $ids
     *
     * @return void
     */
    public function destroy(string $ids)
    {
        QuestionAnswer::withoutGlobalScope('approved')
            ->whereIn('id', explode(',', $ids))
            ->delete();
    }
}
