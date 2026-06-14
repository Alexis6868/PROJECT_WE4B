<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    private const WIKI_API   = 'https://en.wikipedia.org/api/rest_v1/page/summary/';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $geminiApiKey
    ) {}

    public function rechercherChar(string $nom): array
    {
        $prompt = <<<PROMPT
Tu es un expert en véhicules militaires blindés.
Recherche le char ou véhicule militaire nommé "{$nom}" et réponds UNIQUEMENT avec un JSON valide (sans balises markdown, sans texte autour) ayant exactement cette structure :
{
  "nom": "nom officiel complet",
  "pays": "pays d'origine (ex: Allemagne, URSS, États-Unis, Royaume-Uni, France...)",
  "type": "un parmi : Char lourd, Char moyen, Char léger, Transport, Spécial",
  "description": "description factuelle en français, 1-2 phrases, maximum 255 caractères",
  "masse": entier représentant la masse en tonnes,
  "wikipedia_title": "titre exact de l'article Wikipedia anglais sur ce véhicule"
}

Si "{$nom}" n'est pas un véhicule militaire connu, retourne uniquement :
{"erreur": "Véhicule non trouvé"}

Retourne UNIQUEMENT le JSON, sans aucun texte supplémentaire.
PROMPT;

        $payload = [
            'json' => [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'   => 0.1,
                    'maxOutputTokens' => 1024,
                    'thinkingConfig'  => ['thinkingBudget' => 0],
                ],
            ],
            'timeout' => 30,
        ];

        try {
            $response = $this->httpClient->request('POST', self::GEMINI_URL . '?key=' . $this->geminiApiKey, $payload);
            $statusCode = $response->getStatusCode();

            // Retry une fois si 503 (serveur Gemini surchargé)
            if ($statusCode === 503) {
                sleep(3);
                $response = $this->httpClient->request('POST', self::GEMINI_URL . '?key=' . $this->geminiApiKey, $payload);
                $statusCode = $response->getStatusCode();
            }

            if ($statusCode !== 200) {
                return ['erreur' => 'Service IA temporairement indisponible (HTTP ' . $statusCode . '). Réessaie dans quelques secondes.'];
            }

            $data = $response->toArray();
            $parts = $data['candidates'][0]['content']['parts'] ?? [];
            // Cherche la première part qui n'est pas une "pensée" (thinking)
            $text = '';
            foreach ($parts as $part) {
                if (!($part['thought'] ?? false)) {
                    $text = $part['text'] ?? '';
                    break;
                }
            }
            $text = $this->cleanJson($text);
            $result = json_decode($text, true);

            if (!$result || isset($result['erreur'])) {
                return ['erreur' => $result['erreur'] ?? 'Véhicule non trouvé'];
            }

            // Récupère l'image via l'API Wikipedia
            $imageUrl = $this->getWikipediaImage($result['wikipedia_title'] ?? $result['nom']);
            $result['image'] = $imageUrl;
            unset($result['wikipedia_title']);

            return $result;

        } catch (\Exception $e) {
            return ['erreur' => 'Erreur lors de la recherche : ' . $e->getMessage()];
        }
    }

    private function getWikipediaImage(string $title): string
    {
        // 1. Essai direct avec le titre fourni
        $image = $this->fetchWikiSummaryImage($title);
        if ($image) {
            return $image;
        }

        // 2. Fallback : recherche Wikipedia par mot-clé
        try {
            $response = $this->httpClient->request('GET', 'https://en.wikipedia.org/w/api.php', [
                'query' => [
                    'action'   => 'query',
                    'list'     => 'search',
                    'srsearch' => $title . ' tank',
                    'srlimit'  => 3,
                    'format'   => 'json',
                ],
                'timeout' => 8,
                'headers' => ['User-Agent' => 'TankRent/1.0 (academic project)'],
            ]);
            $data = $response->toArray();
            foreach ($data['query']['search'] ?? [] as $result) {
                $image = $this->fetchWikiSummaryImage($result['title']);
                if ($image) {
                    return $image;
                }
            }
        } catch (\Exception) {}

        return '';
    }

    private function fetchWikiSummaryImage(string $title): string
    {
        try {
            $response = $this->httpClient->request('GET', self::WIKI_API . rawurlencode($title), [
                'timeout' => 8,
                'headers' => ['User-Agent' => 'TankRent/1.0 (academic project)'],
            ]);
            $data = $response->toArray();

            return $data['originalimage']['source']
                ?? $data['thumbnail']['source']
                ?? '';
        } catch (\Exception) {
            return '';
        }
    }

    private function cleanJson(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/^```\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);
        // Remplace les caractères de contrôle (saut de ligne, tab...) à l'intérieur des valeurs JSON
        $text = preg_replace_callback('/"(?:[^"\\\\]|\\\\.)*"/u', static function (array $m): string {
            return preg_replace('/[\x00-\x1F\x7F]/', ' ', $m[0]);
        }, $text);
        return $text;
    }
}
