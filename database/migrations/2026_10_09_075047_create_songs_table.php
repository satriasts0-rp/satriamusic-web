<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            // Wajib ada artisnya
            $table->foreignId('artist_id')->constrained()->cascadeOnDelete();
            // Album boleh kosong (nullable) kalau lagunya rilis sebagai Single
            $table->foreignId('album_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('audio_url'); // Path file musiknya nanti
            $table->string('cover_image')->nullable();
            $table->integer('duration')->default(0); // Disimpan dalam format detik
            $table->integer('track_number')->nullable();
            $table->date('release_date')->nullable();
            $table->longText('lyrics')->nullable();
            $table->unsignedBigInteger('plays_count')->default(0);
            $table->boolean('is_explicit')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
