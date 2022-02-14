<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function (Request $request) {
    $accessToken = config('keys.accessToken');
    $conversationSessionToken = config('keys.sessionToken');

    $headers = [
        'x-inbenta-key' => env('CHATBOT_API_KEY'),
        'Authorization' => 'Bearer ' . $accessToken,
        'x-inbenta-session' => 'Bearer ' . $conversationSessionToken
    ];
    $body = [
        'message' => $request->header('userMessage')
    ];

    $response = Http::withHeaders($headers)->post('https://api-gce3.inbenta.io/prod/chatbot/v1/conversation/message', $body);
    $response = json_decode($response);

    return $response;
});
