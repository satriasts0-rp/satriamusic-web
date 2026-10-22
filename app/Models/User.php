<?php

namespace App\Models;

// ... (biarin use yang lain tetap ada)
use Laravel\Sanctum\HasApiTokens; // 1. TAMBAHIN BARIS INI
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // 2. TAMBAHIN HasApiTokens DI DALAM KURUNG KURAWAL INI
    use HasApiTokens, Notifiable; 

    // ... sisa kodingan di bawahnya biarin aja

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function likedSongs() {
        return $this->belongsToMany(Song::class, 'likes', 'user_id', 'song_id');
    }

    public function playlists() {
        return $this->hasMany(Playlist::class);
    }
}
