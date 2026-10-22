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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); 
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        }); // <-- Pastikan ada tutup kurung, tutup kurawal, dan titik koma di sini
    }

    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
}; // <-- Dan pastikan ada tutup kurawal kurawal untuk menutup class