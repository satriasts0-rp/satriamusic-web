<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikedSong extends Model
{
    use HasFactory;

    // Mengizinkan pengisian ID lagu dan ID User
    protected $fillable = ['song_id', 'user_id'];

    // Relasi: Satu 'Like' itu milik satu 'Lagu'
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}