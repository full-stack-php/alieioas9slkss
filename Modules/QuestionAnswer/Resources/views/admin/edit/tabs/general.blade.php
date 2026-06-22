<div class="row">
    <div class="col-md-12">
        {{ Form::text('asker_name', trans('questionanswer::attributes.asker_name'), $errors, $questionanswer, ['required' => true]) }}
        {{ Form::text('asker_phone', trans('questionanswer::attributes.asker_phone'), $errors, $questionanswer, ['required' => true]) }}
        {{ Form::textarea('question', trans('questionanswer::attributes.question'), $errors, $questionanswer, ['required' => true]) }}
        {{ Form::textarea('answer', trans('questionanswer::attributes.answer'), $errors, $questionanswer, ['required' => true]) }}
        {{ Form::checkbox('is_approved', trans('questionanswer::attributes.is_approved'), trans('questionanswer::questions_answers.form.approve_this_question_answer'), $errors, $questionanswer) }}
    </div>
</div>
