<?php

namespace Modules\QuestionAnswer\Http\Requests;

use Modules\Core\Http\Requests\Request;

class UpdateQuestionAnswerRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'questionanswer::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'asker_name' => 'required',
            'asker_phone' => 'required',
            'question' => 'required',
            'answer' => 'required',
            'is_approved' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_approved' => $this->has('is_approved') ? $this->get('is_approved') === 'on' : false,
        ]);
    }
}
