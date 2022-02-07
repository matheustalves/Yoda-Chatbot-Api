<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function (Request $request) {
    $data = Storage::disk('local')->get('api.txt');
    $data = explode(PHP_EOL, $data);

    $accessToken = $data[0];
    $conversationSessionToken = $data[1];

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

    $message = $response->answers[0]->message;

    return json_encode($message);
});
