<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SongController;
use App\Http\Controllers\Api\V1\PlaylistController;

// RUTE AUTENTIKASI
Route::post('v1/auth/register', [AuthController::class, 'register']);
Route::post('v1/auth/login', [AuthController::class, 'login']);
Route::post('v1/auth/reset-password', [AuthController::class, 'resetPassword']);

// RUTE PUBLIK 
Route::prefix('v1')->group(function () {
    Route::get('songs', [SongController::class, 'index']);
    Route::get('songs/search', [SongController::class, 'search']); 
    Route::get('songs/genre/{genre}', [SongController::class, 'getByGenre']);
    Route::get('songs/artist/{artistId}', [SongController::class, 'getByArtist']); 
});

// RUTE TERKUNCI (Wajib Login & Bawa Token Sanctum)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    
    // STUDIO
    Route::post('songs', [SongController::class, 'store']); 
    Route::delete('songs/{song}', [SongController::class, 'destroy']); 

    // LIKED SONGS
    Route::get('liked-songs', [SongController::class, 'likedSongs']);
    Route::post('songs/{id}/like', [SongController::class, 'toggleLike']);
    
    // PLAYLISTS
    Route::apiResource('playlists', PlaylistController::class);
    Route::post('playlists/{playlist}/songs', [PlaylistController::class, 'addSong']);
    Route::delete('playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong']);
});