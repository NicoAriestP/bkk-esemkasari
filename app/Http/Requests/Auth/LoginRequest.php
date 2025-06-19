<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
// Tidak perlu RouteServiceProvider di sini lagi

class LoginRequest extends FormRequest
{
    // ... (authorize, rules, authenticate, ensureIsNotRateLimited, throttleKey, getGuardName tetap sama) ...

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'login_as' => ['required', 'string', 'in:student,partner,user'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $guard = $this->getGuardName();

        if (! Auth::guard($guard)->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->input('email')) . '|' . $this->ip() . '|' . $this->input('login_as')
        );
    }

    public function getGuardName(): string
    {
        $loginAs = $this->input('login_as');

        switch ($loginAs) {
            case 'student':
                return 'student';
            case 'partner':
                return 'partner';
            case 'user':
            default:
                return 'web';
        }
    }

    /**
     * Get the intended redirect path after authentication using named routes.
     */
    public function redirectPath(): string
    {
        $loginAs = $this->input('login_as');

        switch ($loginAs) {
            case 'student':
                return route('dashboard'); // Menggunakan nama route
            case 'partner':
                return route('dashboard'); // Menggunakan nama route
            case 'user':
            default:
                return route('dashboard'); // Menggunakan nama route
        }
    }
}
