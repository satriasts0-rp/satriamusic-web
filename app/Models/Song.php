<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

   protected $fillable = ['title', 'slug', 'genre', 'lyrics', 'audio_url', 'cover_image', 'artist_id'];

    // Relasi ke Artist (Penyanyi)
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    // Relasi ke Album
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Relasi ke Genre (Memperbaiki error "undefined relationship genres")
    public function genres()
    {
        
        return $this->belongsToMany(Genre::class);
        
    }

    
}