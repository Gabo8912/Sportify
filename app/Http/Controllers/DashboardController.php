<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Solo traemos usuarios que sean 'player'
        $players = User::where('role', 'player')
            ->with(['profile', 'videos']) // Cargamos perfil y videos
            ->withCount('videos') // Contamos videos para mostrar actividad
            ->filter($request->only('search', 'position', 'foot')) // Aplicamos filtros
            ->latest()
            ->paginate(9) // 9 Tarjetas por página
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'players' => $players,
            'filters' => $request->only('search', 'position', 'foot'),
        ]);
    }
}