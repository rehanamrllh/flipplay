<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class);

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

Route::post('/media/{medium}/users/{user}', [MediaController::class, 'attach'])
    ->name('media.attach');
Route::delete('/media/{medium}/users/{user}', [MediaController::class, 'detach'])
    ->name('media.detach');
Route::get('/users/{user}/media', [MediaController::class, 'userMedia'])
    ->name('users.media');
