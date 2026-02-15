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
    Schema::create('videos', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Titre de la vidéo
        $table->string('youtube_id')->unique(); // L'ID dans l'URL (ex: dQw4w9WgXcQ)
        $table->text('description')->nullable();
        
        // Colonnes pour l'IA
        $table->text('ai_summary')->nullable(); // Résumé des valeurs chrétiennes
        $table->string('age_range')->nullable(); // Ex: 3-5 ans, 6-10 ans
        $table->boolean('is_safe')->default(false); // Validé par l'IA ou toi
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
