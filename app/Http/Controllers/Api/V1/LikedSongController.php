<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LikedSong;
use App\Models\Song;

class LikedSongController extends Controller
{
    // Fungsi narik daftar lagu yang di-like (Buat menu Library)
    public function index()
    {
        // Ambil semua lagu yang di-like oleh User ID 1 (Karena kita belum bikin fitur Login)
        $likedSongs = LikedSong::with('song.artist')
                        ->where('user_id', 1)
                        ->latest()
                        ->get()
                        ->pluck('song'); // Kita cuma butuh objek 'song'-nya aja buat di-render di Frontend

        return response()->json([
            'message' => 'Liked songs retrieved successfully',
            'data' => $likedSongs
        ], 200);
    }

    // Fungsi klik tombol Love (Kalau udah dilike jadi unlike, kalau belum jadi like)
    public function toggleLike($songId)
    {
        $song = Song::findOrFail($songId);
        $userId = 1; // Dummy User ID 1

        $existingLike = LikedSong::where('song_id', $song->id)
                                 ->where('user_id', $userId)
                                 ->first();

        if ($existingLike) {
            $existingLike->delete(); // Hapus dari favorit (Unlike)
            return response()->json(['message' => 'Unliked', 'is_liked' => false], 200);
        } else {
            LikedSong::create([      // Tambah ke favorit (Like)
                'song_id' => $song->id,
                'user_id' => $userId
            ]);
            return response()->json(['message' => 'Liked', 'is_liked' => true], 201);
        }
    }
}