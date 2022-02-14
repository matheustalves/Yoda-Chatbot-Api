<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Update Token & Conversation Session every 19 minutes
        $schedule->call(function () {
            $headers = [
                'x-inbenta-key' => env('CHATBOT_API_KEY'),
                'Content-Type' => 'application/json'
            ];
            $body = [
                'secret' => env('CHATBOT_API_SECRET')
            ];

            $response = Http::withHeaders($headers)->post('https://api.inbenta.io/v1/auth', $body);
            $response = json_decode($response);

            $accessToken = $response->accessToken;

            $headers = [
                'x-inbenta-key' => env('CHATBOT_API_KEY'),
                'Authorization' => 'Bearer ' . $accessToken
            ];

            $response = Http::withHeaders($headers)->post('https://api-gce3.inbenta.io/prod/chatbot/v1/conversation');
            $response = json_decode($response);

            $sessionToken = $response->sessionToken;

            config(['keys.accessToken' => $accessToken]);
            config(['keys.sessionToken' => $sessionToken]);
        })->cron('*/19 * * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
