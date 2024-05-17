<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $usernameCheck = ($request->input('username') !== '') && ($request->input('username') !== $user->username);

        if ($usernameCheck) {
            $validatedData = $request->validate([
                'username' => ['string', 'max:255', 'unique:users'],
            ], [
                'username.unique' => 'Ez a felhasználónév már foglalt.',
                'username.max' => 'Túl hosszú a felhasználónév.',
            ]);
        } else {
            $validatedData = $request->validate([
                'username' => ['string', 'max:255'],
            ], [
                'username.max' => 'Túl hosszú a felhasználónév.',
            ]);
        }

        if ($usernameCheck) {
            $user->username = $request->input('username');
        }

        try {
            $user->save();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Throwable $th) {
            return Redirect::route('profile.edit')->with('status', 'profile-not-updated');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
