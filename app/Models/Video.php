<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    // Ajoute cette partie :
    protected $fillable = [
        'title',
        'youtube_id',
        'description',
        'ai_summary',  // Autorise l'IA à écrire ici
        'age_range',   // Autorise l'IA à écrire ici
        'is_safe',     // Autorise l'IA à écrire ici
    ];
}