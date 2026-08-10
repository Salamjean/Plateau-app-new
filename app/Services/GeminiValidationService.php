<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiValidationService
{
    protected $apiKey;

    public function __construct()
    {
        // On récupère la clé de l'environnement ou on utilise celle fournie par défaut
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Vérifie si un fichier (image ou PDF) est une pièce d'identité valide.
     *
     * @param \Illuminate\Http\UploadedFile|string $file Fichier à analyser (instance de UploadedFile ou chemin absolu vers le fichier)
     * @return array ['isValid' => bool, 'reason' => string]
     */
    public function validateIdentityDocument($file)
    {
        try {
            if (!$file) {
                return [
                    'isValid' => false,
                    'reason' => "Aucun fichier n'a été fourni."
                ];
            }

            // Récupérer le contenu et le type MIME du fichier
            $fileContent = null;
            $mimeType = null;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                if (!$file->isValid()) {
                    return [
                        'isValid' => false,
                        'reason' => "Le fichier fourni est invalide ou corrompu."
                    ];
                }
                $fileContent = file_get_contents($file->getRealPath());
                $mimeType = $file->getMimeType();
            } elseif (is_string($file) && file_exists($file)) {
                $fileContent = file_get_contents($file);
                $mimeType = mime_content_type($file);
            } else {
                return [
                    'isValid' => false,
                    'reason' => "Le document fourni n'est ni un fichier valide ni un chemin d'accès existant."
                ];
            }

            if (!$fileContent) {
                return [
                    'isValid' => false,
                    'reason' => "Impossible de lire le contenu du document."
                ];
            }

            // Encoder en base64
            $base64Data = base64_encode($fileContent);

            // Préparer la requête vers Gemini API
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

            $prompt = "Tu es un robot de vérification d'identité intelligent pour la mairie de la commune du Plateau. "
                . "Analyse attentivement cette image ou ce document pour déterminer s'il s'agit d'une pièce d'identité officielle valide "
                . "(ex: Carte Nationale d'Identité (CNI), Passeport, Permis de conduire, Carte d'électeur, Carte consulaire, Carte de résident, etc.). "
                . "Réponds strictement en format JSON avec les clés suivantes :\n"
                . "{\n"
                . "  \"is_identity_document\": true ou false,\n"
                . "  \"reason\": \"un court motif en français bien rédigé de pourquoi c'est ou ce n'est pas une pièce d'identité valide\"\n"
                . "}\n"
                . "Attention : Ne mets aucune balise de code markdown, n'écris rien d'autre que l'objet JSON brut.";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Data
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ];

            Log::info("Envoi d'un document à l'IA Gemini pour validation d'identité (Taille: " . strlen($base64Data) . " octets, Type: $mimeType).");

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($url, $payload);

            if ($response->failed()) {
                Log::error("Gemini API a échoué : Status " . $response->status() . " | Body : " . $response->body());
                // En cas de panne de l'API, par sécurité, on peut laisser passer ou rejeter temporairement. 
                // Ici, on renvoie une réponse indiquant que la vérification n'a pas pu aboutir mais on ne bloque pas pour l'expérience utilisateur ou on bloque selon préference.
                return [
                    'isValid' => true, // On autorise par défaut si l'API de validation est HS pour ne pas bloquer les utilisateurs légitimes
                    'reason' => "Vérification automatisée indisponible (Gemini API erreur)."
                ];
            }

            $result = $response->json();
            $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Décoder le texte de réponse JSON
            $jsonData = json_decode(trim($responseText), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Gemini a retourné un JSON invalide: $responseText");
                return [
                    'isValid' => true,
                    'reason' => "Vérification automatisée indisponible (Format de réponse de l'IA invalide)."
                ];
            }

            $isIdentity = isset($jsonData['is_identity_document']) ? (bool) $jsonData['is_identity_document'] : false;
            $reason = $jsonData['reason'] ?? "Le document fourni n'a pas pu être identifié comme une pièce d'identité officielle.";

            Log::info("Résultat validation Gemini : is_identity_document = " . ($isIdentity ? 'true' : 'false') . ", motif = $reason");

            return [
                'isValid' => $isIdentity,
                'reason' => $reason
            ];
        } catch (\Exception $e) {
            Log::error("Erreur durant la validation d'identité avec Gemini : " . $e->getMessage());
            return [
                'isValid' => true, // Tolérance aux pannes
                'reason' => "Erreur interne lors de la validation automatisée."
            ];
        }
    }
}
