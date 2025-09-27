<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/offline', function () {
    return view('offline');
});

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
});
