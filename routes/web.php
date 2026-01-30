<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

//Public routes
Route::get('/player/{id}', [PlayerProfileController::class, 'show'])
    ->name('player.show');
    
//ONLY WITH LOGIN
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        $players = \App\Models\User::with('profile')
            ->where('role','player') 
            ->latest()
            ->get();

            return Inertia::render('Dashboard',[
                'players'=> $players
            ]);
        })->name('dashboard');

    Route::get('/player-profile', [PlayerProfileController::class, 'edit'])
        ->name('player.profile.edit');
        
    Route::patch('/player-profile', [PlayerProfileController::class, 'update'])
        ->name('player.profile.update');

    Route::get('/videos/upload', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
});



require __DIR__.'/settings.php';