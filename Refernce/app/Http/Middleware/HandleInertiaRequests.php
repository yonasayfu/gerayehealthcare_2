<?php

namespace App\Http\Middleware;

use App\Models\Quote;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
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
        $user = $request->user();
        $sharedQuote = null;

        if ($user) {
            // Try pinned first
            $pinned = Quote::query()
                ->where('user_id', $user->id)
                ->where('pinned', true)
                ->first(['id', 'text', 'author', 'image_path']);

            if ($pinned) {
                $author = $pinned->author ?: null;
                $sharedQuote = [
                    'message' => trim($pinned->text),
                    'author' => $author ?: ($user->name ?? 'You'),
                    'image' => $pinned->image_url,
                ];
            } else {
                $userQuotes = Quote::query()
                    ->where('user_id', $user->id)
                    ->orderByRaw('COALESCE(priority, 999999) asc')
                    ->latest()
                    ->get(['id', 'text', 'author', 'image_path']);
                if ($userQuotes->isNotEmpty()) {
                    $picked = $userQuotes->random();
                    $sharedQuote = [
                        'message' => trim($picked->text),
                        'author' => ($picked->author ?: ($user->name ?? 'You')),
                        'image' => $picked->image_url,
                    ];
                }
            }
        }

        if (! $sharedQuote) {
            // Use any quote from the database (global) first
            $any = Quote::query()
                ->orderByRaw('COALESCE(priority, 999999) asc')
                ->inRandomOrder()
                ->first(['id', 'text', 'author', 'image_path']);

            if ($any) {
                $sharedQuote = [
                    'message' => trim($any->text),
                    'author' => trim($any->author ?: '—'),
                    'image' => $any->image_url,
                ];
            } else {
                // Final fallback: Laravel Inspiring
                [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
                $sharedQuote = ['message' => trim($message), 'author' => trim($author)];
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => $sharedQuote,
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'csrf_token' => csrf_token(),
        ];
    }
}
