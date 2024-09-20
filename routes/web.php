<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard',['user' => Auth::user()]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/posts', function() {
            return Inertia::render('Post/List', [
                'user' => Auth::user()
            ]);
        })->name('posts');

        Route::get('/post/{id}', function($id) {
            return Inertia::render('Post/PostDetails', [
                'id' => $id,
            ]);
        })->name('post.view');
    });

    Route::get('/post', function() {
        return Inertia::render('Post/UserPosts', [
            'user' => Auth::user()
        ]);
    })->name('user.post');
});





require __DIR__.'/auth.php';
