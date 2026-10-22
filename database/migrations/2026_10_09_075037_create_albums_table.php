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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel artists. Kalau artis dihapus, albumnya ikut terhapus (cascade)
            $table->foreignId('artist_id')->constrained()->cascadeOnDelete();
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->date('release_date')->nullable();
            $table->string('album_type')->default('album'); // Contoh isi: album, single, ep
            $table->timestamps();
        });
    }
};
