<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\VideoUrlParser;

class VideoController extends Controller
{
    public function create()
    {
        return Inertia::render('Videos/Create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'video_link' => 'required|url',
        ]);

        $videoData = VideoUrlParser::parse($request->video_link);

        if (!$videoData) {
            return back()->withErrors(['video_link' => 'Plataforma no válida. Usa YouTube, Shorts o TikTok.']);
        }

        /** @var User $user */
        $user = $request->user();

        $user->videos()->create([
            'title' => $request->title,
            'video_url' => $request->video_link,
            'platform' => $videoData['platform'],
            'external_video_id' => $videoData['id'],
            'views_count' => 0,
            'likes_count' => 0
        ]);

        return redirect()->back()->with('success', 'Highlight published correctly.');
    }

    public function destroy(Request $request, $id)
    {
        /** @var User $user */
        $user = $request->user();
        
        $video = $user->videos()->findOrFail($id);

        if ($video->platform === 'local' && $video->video_url) {
            Storage::disk('public')->delete($video->video_url);
        }

        $video->delete();

        return back()->with('message', 'Video deleted successfully.');
    }

    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $videos = Video::with([
            'user.profile', 
            'comments.user',
        ])
        ->withCount(['likes', 'saves'])
        ->latest()
        ->cursorPaginate(5);

        
        $videos->through(function ($video) use ($user) {
            if ($user) {
                $video->is_liked = $video->likes()
                    ->where('user_id', $user->id)
                    ->exists();

                $video->is_saved = $video->saves()
                    ->where('user_id', $user->id)
                    ->exists();
            } else {
                $video->is_liked = false;
                $video->is_saved = false;
            }
            
            return $video;
        });

        if (request()->wantsJson()) {
            return $videos;
        }

        return Inertia::render('Feed/Index', [
            'videos' => $videos
        ]);
    }

    // Saves

    public function toggleSave(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $user = Auth::user();

        $existingSave = $video->saves()->where('user_id', $user->id)->first();

        if ($existingSave) {
            $existingSave->delete();
            $msg = 'Video removed from saved.';
        } else {
            $video->saves()->create([
                'user_id' => $user->id
            ]);
            $msg = 'Video saved.';
        }
        
        return back()->with('message', $msg);
    }
}