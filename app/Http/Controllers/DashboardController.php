<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $filters = $request->only(['search', 'position', 'foot', 'club', 'location', 'availability', 'age_min', 'age_max']);

    $players = User::where('role', 'player')
        ->with(['profile', 'videos'])
        ->withCount('videos')
        ->filter($filters)
        ->latest()
        ->paginate(9)
        ->withQueryString();

    return Inertia::render('Dashboard', [
        'players' => $players,
        'filters' => $filters,
    ]);
}
}