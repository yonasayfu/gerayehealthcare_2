<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Support\ModuleAccess;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();
        $roles = [];
        $permissions = [];
        // Load roles and permissions for authenticated users (needed for navigation)
        if ($user) {
            if (method_exists($user, 'getRoleNames')) {
                $roles = $user->getRoleNames()->toArray();
            }
            if (method_exists($user, 'getAllPermissions')) {
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            }
        }
        $modules = $user ? ModuleAccess::forUser($user) : [];

        $flashData = [
            'banner' => $request->session()->get('banner'),
            'bannerStyle' => $request->session()->get('bannerStyle'),
        ];

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'staff_id' => optional($user->staff)->id,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'profile_photo_url' => $user->profile_photo_url ?? null,
                ] : null,
            ],
            'modules' => $modules,
            // Allow toggling conservative route checks in Sidebar (default true = lenient)
            'sidebarLenientRoutes' => true,
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => $flashData,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
