<?php

namespace Modules\User\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Modules\Page\Entities\Page;
use Modules\User\Entities\User;
use Modules\User\LoginProvider;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Http\Requests\LoginRequest;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;

class AuthController extends BaseAuthController
{
    /**
     * Show login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('storefront::public.auth.login', [
            'providers' => LoginProvider::enabled(),
        ]);
    }


    public function getLoginModal()
    {
        return view('storefront::public.auth.login_modal', [
            'providers' => LoginProvider::enabled(),
            'registerUrl' => route('register'),
            'forgottenUrl' => route('reset'),
        ]);
    }

    protected function logoutRedirectTo()
    {
        return route('home');
    }

    public function postLoginModal(LoginRequest $request)
    {
        try {
            $loggedIn = $this->auth->login([
                'email' => $request->email,
                'password' => $request->password,
            ], (bool) $request->get('remember_me', false));

            if (!$loggedIn) {
                return response()->json([
                    'success' => false,
                    'error' => trans('user::messages.users.invalid_credentials'),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'redirect' => $this->redirectTo(),
            ]);
        } catch (NotActivatedException $e) {
            return response()->json([
                'success' => false,
                'error' => trans('user::messages.users.account_not_activated'),
            ], 422);
        } catch (ThrottlingException $e) {
            return response()->json([
                'success' => false,
                'error' => trans('user::messages.users.account_is_blocked', [
                    'delay' => $e->getDelay(),
                ]),
            ], 429);
        }
    }

    /**
     * Redirect the user to the given provider authentication page.
     *
     * @param string $provider
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        if (!LoginProvider::isEnable($provider)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }


    /**
     * Obtain the user information from the given provider.
     *
     * @param string $provider
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        if (!LoginProvider::isEnable($provider)) {
            abort(404);
        }

        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }

        if (User::registered($user->getEmail())) {
            auth()->login(
                User::findByEmail($user->getEmail())
            );

            return redirect($this->redirectTo());
        }

        [$firstName, $lastName] = $this->extractName($user->getName());

        $registeredUser = $this->auth->registerAndActivate([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->getEmail(),
            'phone' => '',
            'password' => str_random(),
        ]);

        $this->assignCustomerRole($registeredUser);

        auth()->login($registeredUser);

        return redirect($this->redirectTo());
    }


    /**
     * Show registrations form.
     *
     * @return Response
     */
    public function getRegister()
    {
        return view('storefront::public.auth.register', [
            'privacyPageUrl' => $this->getPrivacyPageUrl(),
            'providers' => LoginProvider::enabled(),
        ]);
    }


    /**
     * Show reset password form.
     *
     * @return Response
     */
    public function getReset()
    {
        return view('storefront::public.auth.reset.begin');
    }


    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('account.dashboard.index');
    }


    /**
     * The login URL.
     *
     * @return string
     */
    protected function loginUrl()
    {
        return route('login');
    }


    /**
     * Reset complete form route.
     *
     * @param User $user
     * @param string $code
     *
     * @return string
     */
    protected function resetCompleteRoute($user, $code)
    {
        return route('reset.complete', [$user->email, $code]);
    }


    /**
     * Password reset complete view.
     *
     * @return string
     */
    protected function resetCompleteView()
    {
        return view('storefront::public.auth.reset.complete');
    }


    private function extractName($name)
    {
        return explode(' ', $name, 2);
    }


    /**
     * Get privacy page url.
     *
     * @return string
     */
    private function getPrivacyPageUrl()
    {
        return Cache::tags('settings')->rememberForever('privacy_page_url', function () {
            return Page::urlForPage(setting('storefront_privacy_page'));
        });
    }
}
