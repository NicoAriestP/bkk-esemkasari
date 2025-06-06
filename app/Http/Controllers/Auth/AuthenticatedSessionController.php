<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // LoginRequest yang sudah dimodifikasi
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; // Masih bisa digunakan untuk Route::has()
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'), // Asumsi ini rute global
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Menggunakan guard yang tepat dari LoginRequest

        $request->session()->regenerate();

        // LoginRequest->redirectPath() sekarang mengembalikan URL dari named route
        return redirect()->intended($request->redirectPath());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logika logout tetap sama seperti sebelumnya
        $loggedOut = false;
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $loggedOut = true;
        }
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
            $loggedOut = true;
        }
        if (Auth::guard('partner')->check()) {
            Auth::guard('partner')->logout();
            $loggedOut = true;
        }

        if (!$loggedOut && Auth::guard()->check()) {
             Auth::guard()->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
