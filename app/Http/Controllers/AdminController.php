<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video; // Asegúrate de que tu modelo de video se llame así
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'total_videos' => Video::count(),
                'total_views' => Video::sum('views_count') ?? 0, 
                'scouts_count' => User::where('role', 'scout')->count(),
            ],
            //Check users
            'users' => User::latest()->paginate(10, ['*'], 'users_page'),
            
            //Check videos
            'videos' => Video::with('user')->latest()->paginate(5, ['*'], 'videos_page'),
        ]);
    }

    //Delete users
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete an Admin.');
        }

        $user->delete();
        
        return back()->with('message', 'User banned successfully.');
    }
    //Delete videos
    public function deleteVideo($id)
    {
        $video = Video::findOrFail($id);

        $video->delete();
        
        return back()->with('message', 'Video removed by Admin.');
    }
}