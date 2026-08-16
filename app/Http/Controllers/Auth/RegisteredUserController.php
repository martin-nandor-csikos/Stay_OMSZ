<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * Registration is only allowed when no users exist yet.
     */
    public function create(): View|RedirectResponse
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Only the first user is allowed to register, and is granted admin rights.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        $request->validate([
            'charactername' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'charactername.required' => 'Az IC név nem lehet üres.',
            'username.required' => 'A felhasználónév nem lehet üres.',
            'username.unique' => 'Ez a felhasználónév már foglalt.',
            'password.required' => 'A jelszó nem lehet üres.',
            'password.confirmed' => 'A jelszavak nem egyeznek.',
        ]);

        $user = User::create([
            'charactername' => $request->charactername,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'isAdmin' => 1,
            'canGiveAdmin' => 1,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
