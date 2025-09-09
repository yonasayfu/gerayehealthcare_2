<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $quotes = Quote::query()
            ->where('user_id', $user->id)
            ->orderByDesc('pinned')
            ->orderByRaw('COALESCE(priority, 999999) asc')
            ->latest()
            ->get(['id','text','author','language','pinned','priority','image_path','created_at']);

        $trashed = Quote::onlyTrashed()
            ->where('user_id', $user->id)
            ->latest('deleted_at')
            ->get(['id','text','author','language','priority','image_path','deleted_at']);

        return Inertia::render('settings/Quotes', [
            'quotes' => $quotes,
            'trashed' => $trashed,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => ['required','string','max:500'],
            'author' => ['nullable','string','max:120'],
            'language' => ['nullable','string','max:10'],
            'priority' => ['nullable','integer','min:0'],
            'image' => ['nullable','image','max:4096'],
        ]);

        $data = [
            'user_id' => $request->user()->id,
            'text' => $validated['text'],
            'author' => $validated['author'] ?? null,
            'language' => $validated['language'] ?? null,
            'priority' => $validated['priority'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('quotes', 'public');
            $data['image_path'] = $path;
        }

        Quote::create($data);

        return back()->with('status', 'Quote added');
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        abort_unless($quote->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'text' => ['sometimes','string','max:500'],
            'author' => ['sometimes','nullable','string','max:120'],
            'language' => ['sometimes','nullable','string','max:10'],
            'priority' => ['sometimes','nullable','integer','min:0'],
            'pinned' => ['sometimes','boolean'],
            'image' => ['sometimes','nullable','image','max:4096'],
        ]);

        // If pinning, unpin others for this user
        if (array_key_exists('pinned', $validated) && $validated['pinned'] === true) {
            Quote::where('user_id', $quote->user_id)->where('id','!=',$quote->id)->update(['pinned' => false]);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Optionally delete the old image if present
            if ($quote->image_path) {
                \Storage::disk('public')->delete($quote->image_path);
            }
            $path = $request->file('image')->store('quotes', 'public');
            $validated['image_path'] = $path;
        }

        $quote->fill($validated)->save();

        return back()->with('status', 'Quote updated');
    }

    public function destroy(Request $request, Quote $quote): RedirectResponse
    {
        abort_unless($quote->user_id === $request->user()->id, 403);
        $quote->delete();
        return back()->with('status', 'Quote deleted');
    }

    public function restore(Request $request, int $id): RedirectResponse
    {
        $quote = Quote::withTrashed()->where('id', $id)->firstOrFail();
        abort_unless($quote->user_id === $request->user()->id, 403);
        $quote->restore();
        return back()->with('status', 'Quote restored');
    }
}
