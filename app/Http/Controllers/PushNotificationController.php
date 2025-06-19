<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function getFirebaseAccessToken(): ?string
    {
        $serviceAccountFile = base_path('config/privatekeys.json');
        $jwt = json_decode(file_get_contents($serviceAccountFile), true);

        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = base64_encode(json_encode([
            'iss' => $jwt['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $jwt['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signatureBase = "$header.$claim";
        openssl_sign($signatureBase, $signature, $jwt['private_key'], 'sha256WithRSAEncryption');
        $signedJwt = $signatureBase . '.' . base64_encode($signature);

        $ch = curl_init($jwt['token_uri']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $signedJwt,
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        return json_decode($response, true)['access_token'] ?? null;
    }

    public function sendPushNotification($token, $message): bool
    {
        $accessToken = $this->getFirebaseAccessToken();
        if (!$accessToken) {
            Log::error('FCM Access Token could not be retrieved.');
            return false;
        }

        $jwt = json_decode(file_get_contents(base_path('config/privatekeys.json')), true);
        $projectId = $jwt['project_id'];
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $notification = [
            'message' => [
                'token' => $token,
                'data' => [
                    'title' => $message,
                    'body' => $message,
                    'type' => 'custom_value',
                ],
            ],
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json; UTF-8',
            ],
            CURLOPT_POSTFIELDS => json_encode($notification),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        Log::debug($response);

        $json = json_decode($response, true);

        return isset($json['name']);
    }
}
