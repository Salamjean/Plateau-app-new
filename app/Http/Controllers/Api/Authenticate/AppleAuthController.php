<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppleAuthController extends Controller
{
    public function handleAppleAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identity_token'    => 'required|string',
            'name'              => 'nullable|string|max:255',
            'prenom'            => 'nullable|string|max:255',
            'action'            => 'nullable|string|in:login,register',
            'push_notification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $payload = $this->verifyAppleToken($request->identity_token);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jeton Apple invalide ou expiré.'
                ], 401);
            }

            $appleId           = $payload['sub'];
            $email             = $payload['email'] ?? null;
            $nameStr           = $request->name;
            $prenomStr         = $request->prenom;
            $pushNotification  = $request->push_notification;

            $user = User::where('apple_id', $appleId)
                        ->when($email, fn($q) => $q->orWhere('email', $email))
                        ->first();

            $action = $request->input('action');

            if ($user) {
                if ($action === 'register') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ce compte existe déjà. Veuillez plutôt vous connecter.'
                    ], 409);
                }

                if (empty($user->apple_id)) {
                    $user->apple_id = $appleId;
                    $user->save();
                }

                if (!empty($pushNotification) && $user->push_notification !== $pushNotification) {
                    $user->push_notification = $pushNotification;
                    $user->save();
                }

                if ($user->deactivated_at) {
                    $deactivatedAt = \Carbon\Carbon::parse($user->deactivated_at);
                    if (now()->greaterThan($deactivatedAt->copy()->addDays(30))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ce compte a été désactivé définitivement.'
                        ], 403);
                    }
                    $user->update(['deactivated_at' => null]);
                }

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion Apple réussie.',
                    'data'    => [
                        'is_new_user' => false,
                        'user'        => $this->formatUser($user),
                        'token'       => $token,
                        'token_type'  => 'Bearer',
                    ]
                ], 200);
            }

            // ── NOUVEAU COMPTE : stocker en Cache, PAS en base ───────────────
            if ($action === 'login') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun compte trouvé. Veuillez vous inscrire.'
                ], 404);
            }

            $pendingToken = Str::uuid()->toString();

            Cache::put('pending_apple_' . $pendingToken, [
                'name'              => $nameStr,
                'prenom'            => $prenomStr,
                'email'             => $email,
                'apple_id'          => $appleId,
                'push_notification' => $pushNotification,
            ], now()->addHours(1));

            Log::info('Apple Auth - Nouveau compte en attente de finalisation', [
                'email'    => $email,
                'apple_id' => $appleId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Veuillez finaliser votre inscription.',
                'data'    => [
                    'is_new_user'   => true,
                    'pending_token' => $pendingToken,
                    'user'          => [
                        'name'   => $nameStr,
                        'prenom' => $prenomStr,
                        'email'  => $email,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur Apple Auth : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'authentification Apple.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function formatUser(User $user): array
    {
        $pic = $user->profile_picture;
        return [
            'id'              => $user->id,
            'name'            => $user->name,
            'prenom'          => $user->prenom,
            'email'           => $user->email,
            'profile_picture' => $pic
                ? (Str::startsWith($pic, ['http://', 'https://']) ? $pic : Storage::url($pic))
                : null,
        ];
    }

    private function verifyAppleToken(string $identityToken): ?array
    {
        $parts = explode('.', $identityToken);
        if (count($parts) !== 3) {
            return null;
        }

        $header  = json_decode($this->base64UrlDecode($parts[0]), true);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true);

        if (!$header || !$payload) {
            return null;
        }

        // Fetch Apple public keys
        $response = Http::withoutVerifying()->get('https://appleid.apple.com/auth/keys');
        if (!$response->successful()) {
            Log::error('Apple Auth: impossible de récupérer les clés publiques.');
            return null;
        }

        $kid      = $header['kid'] ?? null;
        $alg      = $header['alg'] ?? 'RS256';
        $appleKey = null;

        foreach ($response->json()['keys'] ?? [] as $key) {
            if ($key['kid'] === $kid) {
                $appleKey = $key;
                break;
            }
        }

        if (!$appleKey) {
            Log::error('Apple Auth: clé publique introuvable pour kid=' . $kid);
            return null;
        }

        $pem = $this->jwkToPem($appleKey);
        if (!$pem) {
            return null;
        }

        // Verify signature
        $signingInput = $parts[0] . '.' . $parts[1];
        $signature    = $this->base64UrlDecode($parts[2]);
        $pubKey       = openssl_pkey_get_public($pem);

        if (!$pubKey) {
            return null;
        }

        $algoMap = [
            'RS256' => OPENSSL_ALGO_SHA256,
            'RS384' => OPENSSL_ALGO_SHA384,
            'RS512' => OPENSSL_ALGO_SHA512,
        ];

        if (openssl_verify($signingInput, $signature, $pubKey, $algoMap[$alg] ?? OPENSSL_ALGO_SHA256) !== 1) {
            Log::error('Apple Auth: signature invalide.');
            return null;
        }

        // Validate standard claims
        $bundleId = env('APPLE_CLIENT_IDS', 'com.abibuali09.plateauapps2026');

        if (($payload['iss'] ?? '') !== 'https://appleid.apple.com') {
            Log::error('Apple Auth: iss invalide.');
            return null;
        }

        if (($payload['aud'] ?? '') !== $bundleId) {
            Log::error('Apple Auth: aud invalide — attendu ' . $bundleId . ', reçu ' . ($payload['aud'] ?? 'null'));
            return null;
        }

        if (($payload['exp'] ?? 0) < time()) {
            Log::error('Apple Auth: token expiré.');
            return null;
        }

        return $payload;
    }

    private function jwkToPem(array $jwk): ?string
    {
        if (!isset($jwk['n'], $jwk['e'])) {
            return null;
        }

        $n = $this->base64UrlDecode($jwk['n']);
        $e = $this->base64UrlDecode($jwk['e']);

        $modulus  = chr(0x02) . $this->encodeLength(strlen($n) + (ord($n[0]) & 0x80 ? 1 : 0))
                  . (ord($n[0]) & 0x80 ? "\x00" : '') . $n;
        $exponent = chr(0x02) . $this->encodeLength(strlen($e)) . $e;

        $sequence  = chr(0x30) . $this->encodeLength(strlen($modulus) + strlen($exponent)) . $modulus . $exponent;
        $bitString = chr(0x03) . $this->encodeLength(strlen($sequence) + 1) . "\x00" . $sequence;

        // RSA OID: 1.2.840.113549.1.1.1 + NULL
        $oid = hex2bin('300d06092a864886f70d0101010500');
        $der = chr(0x30) . $this->encodeLength(strlen($oid) + strlen($bitString)) . $oid . $bitString;

        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    private function base64UrlDecode(string $input): string
    {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4 ? strlen($input) + 4 - strlen($input) % 4 : strlen($input), '='));
    }

    private function encodeLength(int $length): string
    {
        if ($length <= 0x7F) {
            return chr($length);
        }
        $bytes = ltrim(pack('N', $length), "\x00");
        return chr(0x80 | strlen($bytes)) . $bytes;
    }
}
