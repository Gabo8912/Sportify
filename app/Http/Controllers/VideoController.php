<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    //Function to show view to upload video
    public function create()
    {
        return Inertia::render('Videos/Create');
    }

    //Safe video to DB Máx 100MB
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'video_file' => 'required|file|mimes:mp4,mov,quicktime|max:102400',
        ]);

        $video_url = $request->file('video_file')->store('videos', 'public');

        $request->user()->videos()->create([
            'title' => $request->title,
            'video_url' => $video_url,
            'views_count' => 0,
            'likes_count' => 0,
        ]);

        return redirect()->route('player.show', $request->user()->id)
            ->with('message', 'Video uploaded successfully! 🎥');
    }
}
