<?php

namespace Modules\User\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Modules\User\Events\CustomerPasswordResetRequested;
use Modules\User\Contracts\Authentication;

class UserResetPasswordController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store($id, Authentication $auth)
    {
        $user = User::findOrFail($id);

        $code = $auth->createReminderCode($user);

        event(new CustomerPasswordResetRequested(
            $user,
            $this->getResetCompleteURL($user, $code),
            $code
        ));

        return redirect()->route('admin.users.index')
            ->withSuccess(trans('user::messages.users.reset_password_email_sent'));
    }


    private function getResetCompleteURL($user, $code)
    {
        return route('admin.reset.complete', [$user->email, $code]);
    }
}
