<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  *');

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
