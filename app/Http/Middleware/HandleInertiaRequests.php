<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $auth = [
            'user' => null, // Default user
            'student' => null,
            'partner' => null,
            'activeGuard' => null,
        ];

        if (Auth::guard('web')->check()) {
            $auth['user'] = Auth::guard('web')->user(); // Or just Auth::user()
            $auth['activeGuard'] = 'web';
        } elseif (Auth::guard('student')->check()) {
            $auth['student'] = Auth::guard('student')->user();
            $auth['activeGuard'] = 'student';
        } elseif (Auth::guard('partner')->check()) {
            $auth['partner'] = Auth::guard('partner')->user();
            $auth['activeGuard'] = 'partner';
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => $auth,
            'tinymce' => [
                'api_key' => config('tinymce.api_key'),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
