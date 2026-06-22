<?php

namespace Modules\QuestionAnswer\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;

class QuestionAnswerTabs extends Tabs
{
    /**
     * Make new tabs with groups.
     *
     * @return void
     */
    public function make()
    {
        $this->group('ask_information', trans('questionanswer::questions_answers.tabs.group.question_answer_information'))
            ->active()
            ->add($this->general());
    }


    private function general()
    {
        return tap(new Tab('questionanswer', trans('questionanswer::questions_answers.tabs.general')), function (Tab $tab) {
            $tab->active();
            $tab->weight(5);
            $tab->fields(['asker_name', 'question', 'answer', 'is_approved']);
            $tab->view('questionanswer::admin.edit.tabs.general');
        });
    }
}
