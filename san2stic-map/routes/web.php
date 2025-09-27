<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::post('/webhook/deploy', [App\Http\Controllers\WebhookController::class, 'deploy'])->name('webhook.deploy');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/upload', App\Livewire\SoundUploader::class)->name('upload');

    Route::get('/sound/{sound}/edit', [App\Http\Controllers\SoundController::class, 'edit'])->name('sound.edit');

    Route::post('/sound/{sound}/trim', [App\Http\Controllers\SoundController::class, 'trim'])->name('sound.trim');

    Route::get('/leaderboard', App\Livewire\Leaderboard::class)->name('leaderboard');

    Route::get('/users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
});
