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
        $emailCheck = ($request->input('email') !== '') && ($request->input('email') !== $user->email);

        if ($emailCheck) {
            $emailRules = [
                'email' => 'email|max:255|unique:users',
            ];
        } else {
            $emailRules = [
                'email' => 'email|max:255',
            ];
        }

        $validator = Validator::make($request->all(), $emailRules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($emailCheck) {
            $user->email = $request->input('email');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->input('password') !== null) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
