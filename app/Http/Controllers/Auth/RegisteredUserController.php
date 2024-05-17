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
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'charactername' => ['required', 'string', 'max:255'],
        ], [
            'charactername.required' => 'Az IC név nem lehet üres.',
            'charactername.required' => 'Túl hosszú az IC név.',
        ]);

        $randomUsername = str_random(8);
        $randomPassword = str_random(8);
        
        $user = User::create([
            'charactername' => $request->charactername,
            'username' => $randomUsername,
            'password' => Hash::make($randomPassword),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
