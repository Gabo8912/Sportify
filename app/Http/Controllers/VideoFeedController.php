<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Video;

class VideoFeedController extends Controller
{
    public function index()
    {
        $videos = Video::with(['user.profile'])
        ->latest()
        ->paginate(10);

        return Inertia::render('Feed/Index', [
            'videos'=> $videos
    ]);
    }
}
