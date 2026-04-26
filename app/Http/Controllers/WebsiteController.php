<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index(Request $request): View
    {
        return view('cms.websites.index', [
            'websites' => $request->user()?->websites()->orderBy('name')->get() ?? collect(),
            'currentWebsiteId' => $request->user()?->current_website_id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        abort_if($user === null, 403);

        $website = Website::create([
            'name' => $payload['name'],
            'domain' => $payload['domain'] ?: null,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $website->ensureDefaultHomeMenu();
        \App\Models\AppSetting::seedDefaultsForWebsite($website);

        $user->websites()->attach($website->id, [
            'role' => 'owner',
            'is_owner' => true,
        ]);

        $user->switchToWebsite($website);

        return redirect()
            ->route('cms.websites.index')
            ->with('status', 'Website created and set as current workspace.');
    }

    public function switch(Request $request, Website $website): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null || ! $user->websites()->whereKey($website->getKey())->exists(), 403);

        $user->switchToWebsite($website);

        return redirect()
            ->back()
            ->with('status', 'Current website switched successfully.');
    }
}
