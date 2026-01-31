<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Like;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoFeedController extends Controller
{
    public function index()
    {
        $videos = Video::with(['user.profile'])
            ->latest()
            ->paginate(10);

        if (auth::check()) {
            $userId = auth::id();
            $videos->getCollection()->transform(function ($video) use ($userId) {
                $video->is_liked = $video->likes()->where('user_id', $userId)->exists();
                return $video;
            });
        }

        return Inertia::render('Feed/Index', [
            'videos' => $videos
        ]);
    }

    public function toggleLike($id)
    {
        $video = Video::findOrFail($id);
        $user = auth::user();

        $existingLike = Like::where('user_id', $user->id)
                            ->where('video_id', $video->id)
                            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $video->decrement('likes_count');
        } else {
            Like::create([
                'user_id' => $user->id,
                'video_id' => $video->id
            ]);
            $video->increment('likes_count');
        }

        return back();
    }

    public function incrementView($id)
    {
        Video::where('id', $id)->increment('views_count');
        return response()->noContent();
    }
}