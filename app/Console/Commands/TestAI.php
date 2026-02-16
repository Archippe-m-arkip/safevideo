<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Video;

class TestAI extends Command
{
    protected $signature = 'ai:test';

    public function handle()
    {
        // 1. On prend la première vidéo qui n'a pas encore été analysée
        $video = Video::whereNull('ai_summary')->first();

        if (!$video) {
            $this->error("Toutes les vidéos sont déjà analysées !");
            return;
        }

        $this->info("🚀 Début de l'analyse  pour : " . $video->title);

        //  2. ÉTAPE TRANSCRIPTION (Priorité 1)
        // $transcript = $this->getTranscription($video->youtube_id);

        // if ($transcript) {
        //     $this->info("✅ Transcription récupérée. Analyse précise en cours...");
        //     $contexte = "Voici la transcription intégrale des paroles : \n" . $transcript;
        // } else {
        //     $this->warn("⚠️ Transcription indisponible. Passage en mode RECHERCHE WEB...");
        //     $contexte = "Je n'ai pas la transcription. Utilise tes capacités de recherche en ligne (Google Search) pour enquêter sur le contenu réel de cette vidéo.";
        // }

        // 3. ENVOI À GEMINI (Avec outils de recherche activés)
        // $prompt = "Tu es un expert en éthique chrétienne et sécurité enfantine. 
        //            Analyse cette vidéo YouTube : https://www.youtube.com/watch?v={$video->youtube_id}
        //            Titre : {$video->title}
        //            {$contexte}

        //            Réponds UNIQUEMENT en JSON brut (pas de texte avant ou après) :
        //            {
        //              'age': 'tranche d'âge recommandée',
        //              'verdict': 'SAFE ou WARNING',
        //              'summary': 'résumé des valeurs chrétiennes et morales détectées'
        //            }";


        // ce prompt permet de faire tout a la fois avec gemini
        // On prépare un prompt "enquêteur"

        $prompt = "Tu es un expert en sécurité parentale chrétienne. 
           Je n'arrive pas à extraire les sous-titres de cette vidéo : https://www.youtube.com/watch?v={$video->youtube_id}
           
           TA MISSION : 
           1. Utilise ta fonction de RECHERCHE GOOGLE pour trouver le script, le résumé détaillé ou les dialogues de cette vidéo.
           2. Analyse si le contenu est 'SAFE' pour un enfant (morale, langage, thèmes).
           3. Réponds UNIQUEMENT en JSON : {'age': '', 'verdict': 'SAFE/WARNING', 'summary': ''}";

        $response = Http::timeout(40)
        ->connectTimeout(30)
        ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . env('GEMINI_API_KEY'), [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'tools' => [
        [
            'google_search' => new \stdClass() // Utilise google_search au lieu de google_search_retrieval
        ]
    ]
]);

        if ($response->successful()) {
            $this->traiterReponseIA($response->json(), $video);
        } else {
            $this->error("Erreur API : " . $response->body());
        }
    }

    private function getTranscription($youtubeId)
{
    try {
        $this->info("Extraction directe via YouTube...");

        // On appelle une API de secours publique qui ne nécessite pas d'installation
        // C'est un service gratuit pour récupérer les transcripts
        $url = "https://subtitles-youtube.com/api/transcript?videoId=" . $youtubeId;
        
        $response = Http::timeout(15)->get($url);

        if ($response->successful()) {
            return $response->body();
        }

        // Si l'API échoue, on tente une deuxième source
        $this->warn("Première source échouée, tentative source 2...");
        $fallbackUrl = "https://youtubetranscript.com/?server_get=2&v=" . $youtubeId;
        // Note: ceci est une simulation, l'extraction de texte pur demande souvent un scraper
        
        return null; 
    } catch (\Exception $e) {
        $this->error("Erreur réseau Ubuntu : " . $e->getMessage());
        return null;
    }
}

    // Méthode pour enregistrer proprement
    private function traiterReponseIA($data, $video)
    {
        $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $cleanJson = trim(str_replace(['```json', '```'], '', $aiText));
        $resultat = json_decode($cleanJson, true);

        if ($resultat) {
            $video->update([
                'ai_summary' => $resultat['summary'],
                'age_range'  => $resultat['age'],
                'is_safe'    => (strtoupper($resultat['verdict']) == 'SAFE'),
            ]);
            $this->info("✅ Analyse enregistrée avec succès !");
        } else {
            $this->error("L'IA a renvoyé un format invalide.");
            $this->line("Texte reçu : " . $aiText);
        }
    }
}