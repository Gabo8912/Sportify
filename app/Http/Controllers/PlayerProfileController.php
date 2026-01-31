<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PlayerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $user->load('profile','videos');

        return Inertia::render('Player/Edit', [
            'profile' => $user->profile,
            'user' => $user
        ]);
    }

    // Actualiza Datos Personales y Perfil
    public function update(Request $request)
    {
        $user = $request->user();

        // 1. Validar todo junto
        $validated = $request->validate([
            // Datos de Cuenta (Tabla Users)
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,

            // Datos de Perfil (Tabla Profiles)
            'birth_date' => 'required|date',
            'current_club' => 'nullable|string|max:100', // Usado por Scouts y Players
            
            // Solo para Players (nullable para que el Scout no falle)
            'position' => 'nullable|string|max:50',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:150',
            'dominant_foot' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        $profileData = $request->only([
            'birth_date', 'current_club', 'position', 
            'height', 'weight', 'dominant_foot'
        ]);

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return Redirect::route('player.profile.edit')->with('success', 'Profile updated successfully.');
    }

    // NUEVO: Método exclusivo para cambiar contraseña
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function show($id){
        $user = User::with('profile', 'videos')->findOrFail($id);
        return Inertia::render('Player/Show', ['player' => $user]);
    }

    
}