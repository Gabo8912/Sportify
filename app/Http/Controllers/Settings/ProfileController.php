<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */


    public function updatePhoto(Request $request): RedirectResponse
{
    $request->validate([
        'photo' => ['required', 'image', 'max:5120'],
    ]);

    $user = $request->user();

    if (!$user->profile) {
        return back()->withErrors(['photo' => '⚠️ First you must save your "Personal Information" (Date of Birth) before uploading a photo.']);
    }


    $profile = $user->profile;

    if ($profile->profile_photo_path) {
        Storage::disk('public')->delete($profile->profile_photo_path);
    }

    $path = $request->file('photo')->store('profile-photos', 'public');

    $profile->update([
        'profile_photo_path' => $path
    ]);

    return back();
}
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
