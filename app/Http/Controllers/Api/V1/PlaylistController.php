<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index(Request $request) {
        return response()->json(['data' => $request->user()->playlists]);
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        $playlist = $request->user()->playlists()->create(['name' => $request->name]);
        return response()->json(['message' => 'Playlist dibuat!', 'data' => $playlist]);
    }

    public function show(Playlist $playlist, Request $request) {
        if ($playlist->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        $playlist->load('songs.artist');
        return response()->json(['data' => $playlist]);
    }

    public function destroy(Playlist $playlist, Request $request) {
        if ($playlist->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        $playlist->delete();
        return response()->json(['message' => 'Playlist dihapus!']);
    }

    public function addSong(Request $request, Playlist $playlist) {
        if ($playlist->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        $request->validate(['song_id' => 'required|exists:songs,id']);
        $playlist->songs()->syncWithoutDetaching([$request->song_id]);
        return response()->json(['message' => 'Lagu ditambahkan!']);
    }

    public function removeSong(Playlist $playlist, $songId, Request $request) {
        if ($playlist->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        $playlist->songs()->detach($songId);
        return response()->json(['message' => 'Lagu dihapus!']);
    }
}