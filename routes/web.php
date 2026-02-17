<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;



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
    

    Route::get('/player-profile', [PlayerProfileController::class, 'edit'])->name('player.profile.edit');
    Route::get('/scout-profile', [PlayerProfileController::class, 'edit'])->name('scout.profile.edit');
    Route::patch('/player-profile', [PlayerProfileController::class, 'update'])->name('player.profile.update');
    Route::post('/player/{id}/follow', [App\Http\Controllers\PlayerProfileController::class, 'toggleFollow'])->name('player.follow');
    Route::put('/player-profile/password', [PlayerProfileController::class, 'updatePassword'])->name('player.password.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    

    //Videos
    Route::get('/videos/upload', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::get('/feed', [App\Http\Controllers\VideoFeedController::class, 'index'])->name('feed');
    Route::post('/videos/{id}/like', [App\Http\Controllers\VideoFeedController::class, 'toggleLike'])->name('videos.like');
    Route::post('/videos/{id}/view', [App\Http\Controllers\VideoFeedController::class, 'incrementView'])->name('videos.view');
    Route::post('/videos/{video}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');
    Route::post('/videos/{video}/save', [VideoController::class, 'toggleSave'])->name('videos.save');

    //Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    //Follow
    Route::post('/follow/{user}', [App\Http\Controllers\FollowController::class, 'toggle'])->name('follow.toggle');
    Route::get('/following', [App\Http\Controllers\FollowController::class, 'following'])->name('following.index');
    Route::get('/followers', [App\Http\Controllers\FollowController::class, 'followers'])->name('followers.index');

    //Admin
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/videos/{id}', [AdminController::class, 'deleteVideo'])->name('admin.videos.delete');
    });



    
});


require __DIR__.'/settings.php';