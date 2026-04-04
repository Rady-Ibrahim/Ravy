<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\Admin\AdminLoginRequest;
use Modules\Auth\Services\Admin\LoginService;
use Modules\Auth\Services\Admin\LogoutService;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth::admin.login');
    }

    public function login(AdminLoginRequest $request, LoginService $loginService): RedirectResponse
    {
        $loginService->login($request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request, LogoutService $logoutService): RedirectResponse
    {
        $logoutService->logout($request);

        return redirect()->route('admin.auth.login');
    }
}
