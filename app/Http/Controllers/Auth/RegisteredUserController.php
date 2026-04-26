<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Website;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'website_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $payload['name'],
            'phone' => $payload['phone'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
        ]);

        $website = Website::create([
            'name' => $payload['website_name'],
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $website->ensureDefaultHomeMenu();

        $user->websites()->attach($website->id, [
            'role' => 'owner',
            'is_owner' => true,
        ]);

        $user->switchToWebsite($website);

        if (Schema::hasTable('roles')) {
            $appUserRole = \Spatie\Permission\Models\Role::query()
                ->where('name', 'user')
                ->where('guard_name', 'web')
                ->first();

            if ($appUserRole !== null) {
                $user->assignRole($appUserRole);
            }
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('status', 'Account created successfully. Public pages are available immediately, while CMS access remains limited to administrator roles.');
    }
}