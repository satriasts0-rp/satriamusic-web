<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSongRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ubah jadi true dulu sementara untuk testing
        return true; 
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'album_id' => 'nullable|exists:albums,id',
            'artist_id' => 'required|exists:artists,id',
            'audio' => 'required|file|mimes:mp3,wav|max:20480', // Max 20MB
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}