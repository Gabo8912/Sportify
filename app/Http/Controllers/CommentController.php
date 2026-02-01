<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $videoId)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:500',
        ]);

        Comment::create([
            'body' => $validated['body'],
            'user_id' => Auth::id(),
            'video_id' => $videoId,
        ]);

        return back(); // Regresa a la misma página para ver el comentario
    }
}