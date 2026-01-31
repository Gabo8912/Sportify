<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;



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

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    

    Route::get('/player-profile', [PlayerProfileController::class, 'edit'])
        ->name('player.profile.edit');
        
    Route::patch('/player-profile', [PlayerProfileController::class, 'update'])
        ->name('player.profile.update');

    Route::put('/player-profile/password', [PlayerProfileController::class, 'updatePassword'])->name('player.password.update');

    //Videos
    Route::get('/videos/upload', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::get('/feed', [App\Http\Controllers\VideoFeedController::class, 'index'])->name('feed');   
    Route::post('/videos/{id}/like', [App\Http\Controllers\VideoFeedController::class, 'toggleLike'])->name('videos.like');
    Route::post('/videos/{id}/view', [App\Http\Controllers\VideoFeedController::class, 'incrementView'])->name('videos.view'); 
    Route::get('/player/{id}', [App\Http\Controllers\PlayerProfileController::class, 'show'])->name('player.show');


    //Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    
});


require __DIR__.'/settings.php';