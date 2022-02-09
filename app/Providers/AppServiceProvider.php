<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $headers = [
            'x-inbenta-key' => env('CHATBOT_API_KEY'),
            'Content-Type' => 'application/json'
        ];
        $body = [
            'secret' => env('CHATBOT_API_SECRET')
        ];

        $response = Http::withHeaders($headers)->post('https://api.inbenta.io/v1/auth', $body);
        $response = json_decode($response);

        $accessToken = null;
        if (!empty($response->accessToken)) {
            $accessToken = $response->accessToken;
        }

        $headers = [
            'x-inbenta-key' => env('CHATBOT_API_KEY'),
            'Authorization' => 'Bearer ' . $accessToken
        ];

        $response = Http::withHeaders($headers)->post('https://api-gce3.inbenta.io/prod/chatbot/v1/conversation');
        $response = json_decode($response);

        $sessionToken = $response->sessionToken;

        Storage::disk('local')->put('api.txt', $accessToken . PHP_EOL . $sessionToken);
    }
}
