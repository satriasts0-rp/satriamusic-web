<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SongService
{
    public function storeSong(array $data, UploadedFile $audio, ?UploadedFile $coverImage = null): Song
    {
        return DB::transaction(function () use ($data, $audio, $coverImage) {
            // 1. Simpan File Audio
            $audioPath = $audio->store('audio/songs', 'public');
            
            // 2. Simpan Cover Image (jika ada)
            $coverPath = null;
            if ($coverImage) {
                $coverPath = $coverImage->store('images/songs', 'public');
            }

            // 3. Simpan ke Database
            $song = Song::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title'] . '-' . uniqid()),
                'artist_id' => $data['artist_id'],
                'album_id' => $data['album_id'] ?? null,
                'audio_url' => $audioPath,
                'cover_image' => $coverPath,
                'duration' => 0, // Nanti diupdate via Background Job
                'is_active' => true,
            ]);

            // Hapus bagian genres agar tidak menyentuh tabel yang belum ada
            return $song->load(['artist']);
        });
    }
}