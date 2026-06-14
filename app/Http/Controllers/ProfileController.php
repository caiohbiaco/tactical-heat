<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** GET /profile */
    public function show()
    {
        $user    = auth()->user();
        $stats   = [
            'searches' => $user->searches()->count(),
            'cities'   => $user->searches()->distinct('city_name')->count('city_name'),
            'reports'  => $user->searches()->whereHas('report')->count(),
        ];

        return view('profile.show', compact('user', 'stats'));
    }

    /** POST /profile/info — atualiza nome, email e bio */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio'   => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'bio'   => $request->bio,
        ]);

        return back()->with('success_info', 'Informações atualizadas com sucesso.');
    }

    /** POST /profile/password — altera senha */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()
                ->withErrors(['current_password' => 'A senha atual está incorreta.'])
                ->with('tab', 'password');
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Senha alterada com sucesso.')->with('tab', 'password');
    }

    /** POST /profile/avatar — faz upload da foto */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Remove avatar antigo se existir
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success_avatar', 'Foto de perfil atualizada.');
    }

    /** DELETE /profile/avatar — remove a foto */
    public function removeAvatar()
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success_avatar', 'Foto removida.');
    }

    /** DELETE /profile — exclui a conta */
    public function destroy(Request $request)
    {
        $request->validate([
            'confirm_delete' => ['required', 'in:EXCLUIR'],
        ]);

        $user = auth()->user();

        Auth::logout();

        // Remove avatar do storage
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Conta excluída com sucesso.');
    }
}