<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary; // Pakai mesin asli Cloudinary biar kebal error

class SongController extends Controller
{
    public function index()
    {
        $songs = \App\Models\Song::with('artist')->where('is_active', true)->latest()->get();
        return response()->json(['message' => 'Success fetch songs', 'data' => $songs], 200);
    }
    
    public function store(\Illuminate\Http\Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Hanya Admin yang boleh upload lagu bro!'], 403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'artist_name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'lyrics' => 'nullable|string', 
            'audio_file' => 'required|mimes:mp3,wav|max:20480',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $artist = \App\Models\Artist::firstOrCreate(
            ['name' => $request->artist_name],
            ['slug' => \Illuminate\Support\Str::slug($request->artist_name)]
        );

        // JURUS PAMUNGKAS: Hardcode link Cloudinary langsung di sini!
            $cloudinary = new Cloudinary('cloudinary://667125636527865:ozuNIyRG6nO2BNRbYha1g2Gs8NE@epprrhgd');
       
        // MESIN UPLOAD MP3 KE CLOUDINARY
        $audioUpload = $cloudinary->uploadApi()->upload($request->file('audio_file')->getRealPath(), [
            'folder' => 'satriamusic/audio',
            'resource_type' => 'video' 
        ]);
        $audioPath = $audioUpload['secure_url'];

        // MESIN UPLOAD GAMBAR KE CLOUDINARY
        $coverPath = null;
        if ($request->hasFile('cover_file')) {
            $coverUpload = $cloudinary->uploadApi()->upload($request->file('cover_file')->getRealPath(), [
                'folder' => 'satriamusic/covers'
            ]);
            $coverPath = $coverUpload['secure_url'];
        }

        $song = \App\Models\Song::create([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title . '-' . time()),
            'artist_id' => $artist->id,
            'genre' => $request->genre,
            'lyrics' => $request->lyrics, 
            'audio_url' => $audioPath,
            'cover_image' => $coverPath,
            'is_active' => true,
        ]);
        
        return response()->json([
            'message' => 'Lagu berhasil diupload ke Web Studio!',
            'data' => $song->load('artist')
        ], 201);
    }

    public function getByGenre($genre)
    {
        $songs = \App\Models\Song::with('artist')->where('is_active', true)->where('genre', 'LIKE', "%{$genre}%")->latest()->get();
        return response()->json(['message' => "Lagu dengan genre: $genre", 'data' => $songs], 200);
    }

    public function getByArtist($artistId)
    {
        $songs = \App\Models\Song::with('artist')->where('artist_id', $artistId)->where('is_active', true)->latest()->get();
        $artist = \App\Models\Artist::find($artistId);
        if (!$artist) return response()->json(['message' => 'Artis tidak ditemukan'], 404);
        return response()->json(['artist' => $artist, 'data' => $songs]);
    }
    
    public function search(\Illuminate\Http\Request $request)
    {
        $query = $request->get('q');
        $songs = \App\Models\Song::with('artist')
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhereHas('artist', function($q2) use ($query) {
                      $q2->where('name', 'like', "%{$query}%");
                  });
            })->get();
        return response()->json(['data' => $songs]);
    }

    public function toggleLike($id, \Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $hasLiked = $user->likedSongs()->where('song_id', $id)->exists();
        if ($hasLiked) {
            $user->likedSongs()->detach($id);
            return response()->json(['message' => 'Unliked', 'is_liked' => false]);
        } else {
            $user->likedSongs()->attach($id);
            return response()->json(['message' => 'Liked', 'is_liked' => true]);
        }
    }

    public function likedSongs(\Illuminate\Http\Request $request)
    {
        $songs = $request->user()->likedSongs()->with('artist')->where('is_active', true)->latest()->get();
        return response()->json(['data' => $songs]);
    }
}