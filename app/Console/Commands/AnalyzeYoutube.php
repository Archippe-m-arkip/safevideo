<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Video;

class AnalyzeYoutube extends Command
{
    protected $signature = 'youtube:analyze {url}';
    protected $description = 'Analyse YouTube robuste avec Debug intégré';

    public function handle()
    {
        $url = $this->argument('url');
        $videoId = $this->extractId($url);

        if (!$videoId) {
            $this->error("❌ URL invalide.");
            return;
        }

        $this->info("📡 Récupération du titre...");
        $officialTitle = $this->getOfficialTitle($videoId) ?? "Vidéo YouTube";

        $video = Video::where('youtube_id', $videoId)->first();
        if ($video && $video->ai_summary) {
            $this->info("ℹ️ Déjà analysée : [{$officialTitle}]");
            if (!$this->confirm('Forcer une nouvelle analyse ?', false)) return;
        }

        $this->info("🔍 Analyse en cours : $officialTitle");
        $transcript = $this->fetchTranscript($videoId);
        
        $analysis = $this->askGemini($videoId, $officialTitle, $transcript);

        if ($analysis) {
            $video = Video::updateOrCreate(
                ['youtube_id' => $videoId],
                [
                    'title'       => $analysis['title'] ?? $officialTitle,
                    'description' => $analysis['official_description'] ?? 'Pas de description', 
                    'ai_summary'  => $analysis['summary'] ?? 'Analyse indisponible',
                    'age_range'   => $analysis['age'] ?? 'Non défini',
                    'is_safe'     => (isset($analysis['verdict']) && strtoupper($analysis['verdict']) === 'SAFE'),
                ]
            );

            $this->info("✅ Enregistré : " . $video->title);
        } else {
            // Le message d'erreur précis est maintenant géré dans askGemini
            $this->error("❌ L'analyse a échoué.");
        }
    }

    private function getOfficialTitle($videoId) {
        try {
            $response = Http::timeout(10)->get("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=$videoId&format=json");
            return $response->successful() ? $response->json()['title'] : null;
        } catch (\Exception $e) { return null; }
    }

    private function fetchTranscript($videoId) {
        $this->line("⏳ Récupération du texte...");
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => env('RAPIDAPI_KEY'),
                'X-RapidAPI-Host' => 'youtube-transcript3.p.rapidapi.com'
            ])->timeout(20)->get("https://youtube-transcript3.p.rapidapi.com/api/transcripts/$videoId");
            return ($response->successful() && isset($response->json()['lines'])) 
                ? collect($response->json()['lines'])->pluck('text')->implode(' ') 
                : null;
        } catch (\Exception $e) { return null; }
    }

    private function askGemini($videoId, $title, $transcript)
    {
        $this->line("🤖 Consultation de Gemini...");
        $apiKey = trim(env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent";

        $prompt = "Tu es ParentVigilant AI. Analyse cette vidéo (ID: $videoId).
        Titre actuel : $title
        Transcription : " . ($transcript ?: "INDISPONIBLE. Utilise Google Search pour trouver la description et le contenu.") . "

        RÉPONDS UNIQUEMENT EN JSON :
        {
          \"title\": \"Le vrai titre\",
          \"official_description\": \"La description officielle\",
          \"summary\": \"Analyse chrétienne courte\",
          \"age\": \"3-5, 6-11, 12-16, ou 17+\",
          \"verdict\": \"SAFE ou WARNING\"
        }";

        try {
            $response = Http::timeout(120)
                ->withQueryParameters(['key' => $apiKey])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'tools' => [['google_search' => new \stdClass()]]
                ]);

            if (!$response->successful()) {
                $this->error("❌ Erreur API Google : Status " . $response->status());
                $this->line($response->body()); // Affiche la raison (ex: Clé invalide)
                return null;
            }

            $result = $response->json();
            
            // Vérifier si l'IA a refusé de répondre pour raisons de sécurité
            if (isset($result['candidates'][0]['finishReason']) && $result['candidates'][0]['finishReason'] === 'SAFETY') {
                $this->warn("⚠️ Google a bloqué l'analyse car le contenu de la vidéo semble trop sensible/dangereux.");
                return null;
            }

            $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Extraction JSON ultra-robuste
            if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                $data = json_decode($matches[0], true);
                if ($data) {
                    $data['age'] = trim(str_ireplace(['Pour les ', ' ans', 'Âge :'], '', $data['age'] ?? 'Non défini'));
                    return $data;
                }
            }

            $this->error("⚠️ Impossible de décoder le JSON de l'IA. Voici ce qu'elle a dit :");
            $this->line($rawText); // Utile pour voir si l'IA a fait une erreur de syntaxe

        } catch (\Exception $e) {
            $this->error("❌ Crash : " . $e->getMessage());
        }
        return null;
    }

    private function extractId($url) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1] ?? null;
    }
}