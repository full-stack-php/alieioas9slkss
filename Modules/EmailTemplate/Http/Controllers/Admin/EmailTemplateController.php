<?php

namespace Modules\EmailTemplate\Http\Controllers\Admin;

use Modules\Admin\Traits\HasCrudActions;
use Illuminate\Http\Request;
use Modules\Order\Entities\OrderStatus;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Http\Requests\SaveEmailTemplateRequest;
use Illuminate\Http\JsonResponse;
use Modules\EmailTemplate\Services\EmailTemplateTestSender;
use Modules\EmailTemplate\Http\Requests\SendTestEmailTemplateRequest;
use Throwable;

class EmailTemplateController
{
    use HasCrudActions;

    protected $model = EmailTemplate::class;

    protected $label = 'emailtemplate::email_templates.email_template';

    protected $viewPath = 'emailtemplate::admin.email_templates';

    protected $validation = SaveEmailTemplateRequest::class;

    protected function formData(): array
    {
        return [
            'types' => EmailTemplateType::all(),
            'recipients' => EmailTemplateType::recipients(),
            'statusKeys' => OrderStatus::list(),
            'shortcodesByType' => EmailTemplateType::shortcodesByType(),
        ];
    }

    public function index(Request $request)
    {
        if ($request->has('query')) {
            return $this->getModel()
                ->search($request->get('query'))
                ->query()
                ->limit($request->get('limit', 10))
                ->get();
        }

        return view("{$this->viewPath}.index", [
            'types' => EmailTemplateType::all(),
            'recipients' => EmailTemplateType::recipients(),
        ]);
    }

    public function sendTest(
        SendTestEmailTemplateRequest $request,
        EmailTemplateTestSender $testSender
    ): JsonResponse {
        $payload = $request->validated();

        $email = $payload['test_email'];

        unset($payload['test_email']);

        try {
            $testSender->send($email, $payload);
        } catch (Throwable) {
            return response()->json([
                'message' => trans('emailtemplate::email_templates.form.test_email_failed'),
            ], 500);
        }

        return response()->json([
            'message' => trans('emailtemplate::email_templates.form.test_email_sent'),
        ]);
    }
}
