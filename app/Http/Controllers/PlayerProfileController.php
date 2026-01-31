<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PlayerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        
        $user->load('profile', 'videos');

        return Inertia::render('Player/Edit', [
            'profile' => $user->profile,
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $isPlayer = $user->role === 'player';

        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cover_photo' => ['nullable', 'image', 'max:10240'],
            'birth_date' => ['required', 'date'], 
            'current_club' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'availability_status' => ['nullable', 'string', Rule::in(['Available', 'Looking for Club', 'Under Contract', 'Injured'])],
            'position' => [$isPlayer ? 'required' : 'nullable', 'string', 'max:50'],
            'height' => [$isPlayer ? 'required' : 'nullable', 'integer', 'min:100', 'max:250'],
            'weight' => [$isPlayer ? 'required' : 'nullable', 'integer', 'min:30', 'max:150'],
            'dominant_foot' => [$isPlayer ? 'required' : 'nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);


        $profileData = [
            'birth_date' => $validated['birth_date'],
            'current_club' => $validated['current_club'] ?? '',
            'location' => $validated['location'] ?? '',
            'availability_status' => $validated['availability_status'] ?? 'Available',
            'position' => $validated['position'] ?? 'N/A',
            'height' => $validated['height'] ?? 0,
            'weight' => $validated['weight'] ?? 0,
            'dominant_foot' => $validated['dominant_foot'] ?? 'Right',
        ];

        if ($request->hasFile('cover_photo')) {
        if ($user->profile && $user->profile->cover_photo_path) {
            Storage::disk('public')->delete($user->profile->cover_photo_path);
        }
        $path = $request->file('cover_photo')->store('covers', 'public');
        $profileData['cover_photo_path'] = $path;
    }

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return Redirect::route('player.profile.edit')->with('success', 'Profile updated successfully.');
    }

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

    public function show($id)
    {
        $currentUser = Auth::user();

        $player = User::where('id', $id)
            ->with([
                'profile',
                'videos' => fn($q) => $q->latest(), 
                'followers.profile',
                'following.profile'
            ])
            ->withCount(['videos', 'followers', 'following'])
            ->findOrFail($id);

        $isFollowing = false;
        if ($currentUser) {
            $isFollowing = $currentUser->following()->where('followed_id', $id)->exists();
        }

        return Inertia::render('Player/Show', [
            'player' => $player,
            'isFollowing' => $isFollowing,
            'canFollow' => $currentUser && $currentUser->id !== $player->id
        ]);
    }

    public function toggleFollow(Request $request, $id)
    {
        $user = $request->user();

        if ($user->id == $id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        $user->following()->toggle($id);

        return back(); 
    }
}