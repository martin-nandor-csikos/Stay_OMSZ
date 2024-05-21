<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'A megadott 2 jelszó nem egyezik meg.',
            'password.required' => 'Meg kell adnod egy új jelszót.',
            'password.min' => 'Túl rövid a jelszó. Minimum 8 karakterből kell állnia.',

            'current_password.required' => 'Meg kell adnod a jelenlegi jelszavad.',
            'current_password.current_password' => 'Hibás jelszó.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function updateUserPassword(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
 
        $validated = $request->validateWithBag('updatePassword', [
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'A megadott 2 jelszó nem egyezik meg.',
            'password.required' => 'Meg kell adnod egy új jelszót.',
            'password.min' => 'Túl rövid a jelszó. Minimum 8 karakterből kell állnia.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        DB::table('admin_logs')->insert(
            ['user_id' => Auth::user()->id, 'didWhat' => 'Frissítette a(z) ' . $user->id . ' ID-val rendelkező felhasználó jelszavát']
        );

        return back()->with('password-updated', 'A jelszó sikeresen frissült.');
    }
}