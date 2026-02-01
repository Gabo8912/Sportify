<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user) {
        if ($request->user()->id === $user->id) return back();

        $request->user()->following()->toggle($user->id);
        
        return back();
        }
        // App/Http/Controllers/FollowController.php

public function following()
{
    return Inertia::render('Follows/Following', [
        'following' => Auth::user()->following()->with('profile')->get()
    ]);
}

public function followers()
{
    return Inertia::render('Follows/Followers', [
        'followers' => Auth::user()->followers()->with('profile')->get()
    ]);
}

    
}

