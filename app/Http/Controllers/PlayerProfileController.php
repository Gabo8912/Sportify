<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Profile; // Import model
use Inertia\Inertia; // Import Inertia for Vue


class PlayerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user(); //ID user

        $user->load('profile'); //Search profile

        //Show data
        return Inertia::render('Player/Edit', [
            'profile' => $user->profile,
            'user' => $user
        ]);
    }

    //To update data from profile
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'position' => 'nullable|string|max:50',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:150',
            'current_club' => 'nullable|string|max:100',
        ]);


        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return Redirect::route('player.profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
