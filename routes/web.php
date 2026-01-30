<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PlayerProfileController;

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
    
    // Tu Dashboard original
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/player-profile', [PlayerProfileController::class, 'edit'])
        ->name('player.profile.edit');
        
    Route::patch('/player-profile', [PlayerProfileController::class, 'update'])
        ->name('player.profile.update');
});



require __DIR__.'/settings.php';