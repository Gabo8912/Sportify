<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Inertia\Inertia;
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
        // Validación
        $request->validate([
            'title' => 'required|string|max:100',
            'video_link' => 'required|url',
        ]);

        // Usamos el Parser (que te doy más abajo)
        $videoData = VideoUrlParser::parse($request->video_link);

        if (!$videoData) {
            return back()->withErrors(['video_link' => 'Plataforma no válida. Usa YouTube, Shorts o TikTok.']);
        }

        // Crear el video
        // NOTA: Asegúrate de actualizar el modelo Video.php (Paso 3)
        $request->user()->videos()->create([
            'title' => $request->title,
            'video_url' => $request->video_link, 
            'platform' => $videoData['platform'],      // Guarda 'youtube'
            'external_video_id' => $videoData['id'],   // Guarda el ID (ej: YGJPYh713d0)
            'views_count' => 0,
            'likes_count' => 0
        ]);

        // Volvemos atrás (así evitamos el error de ruta no encontrada)
        return redirect()->back()->with('success', 'Highlight published correctly.');
    }

    public function destroy(Request $request, $id)
    {
        $video = $request->user()->videos()->findOrFail($id);

        // SOLO borramos el archivo si es 'local'. Si es YouTube, no tocamos el storage.
        if ($video->platform === 'local' && $video->video_url) {
            Storage::disk('public')->delete($video->video_url);
        }

        $video->delete();

        return back()->with('message', 'Video deleted successfully.');
    }

    public function index()
    {
        // Usamos cursorPaginate para el scroll infinito estilo TikTok
        $videos = Video::with([
            'user.profile', // Asegúrate de que esta relación exista en tu User.php
            'comments.user',
        ])
        ->withCount('likes')
        ->latest()
        ->cursorPaginate(5); // Carga de 5 en 5 para mayor velocidad

        // Si el Frontend (Vue) pide JSON (cuando bajas haciendo scroll), devolvemos solo datos
        if (request()->wantsJson()) {
            return $videos;
        }

        // Si es la primera carga, devolvemos la página completa
        return Inertia::render('Feed/Index', [
            'videos' => $videos
        ]);
    }
}