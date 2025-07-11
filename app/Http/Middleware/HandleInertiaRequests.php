<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

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
        return array_merge(parent::share($request), [
            'appVersion' => env('APP_VERSION', '1.0.0'),
            'appName' => env('APP_NAME', 'Laravel'),
            'flash' => function () use ($request) {
                return [
                    'message' => $request->session()->get('message'),
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'auth' => Auth::check() ? [
                'user' => [
                    'username' => Auth::user()->username,
                    'name' => Auth::user()->profile->getFullName(),
                    'email' => Auth::user()->email,
                    'isAdmin' => (bool) Auth::user()->is_admin,
                    'isFirstLogin' => (bool) Auth::user()->is_first_login,
                    'type' => Auth::user()->profile->type,
                ]
            ] : null,
            'token' => Auth::check() ? Auth::user()->api_token : null,
        ]);
    }
}
