<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $email = $request->string('email')->toString();

        if (! Auth::attempt($request->validated())) {
            $this->logActivity(
                $request,
                'login_failed',
                'Percobaan login gagal',
                null,
                [
                    'email_hash' => hash('sha256', mb_strtolower(trim($email))),
                ]
            );

            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->logActivity($request, 'login', 'Pengguna berhasil login');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logActivity($request, 'logout', 'Pengguna logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
