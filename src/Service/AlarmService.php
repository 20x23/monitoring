<?php

declare(strict_types=1);

namespace Monitoring\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AlarmService
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Sendet eine Benachrichtigung via Telegram Bot API.
     */
    public function sendTelegramNotification(
        string $apiToken,
        string $chatId,
        string $url,
        string $error
    ): void {
        $message = "⚠️ Fehler bei {$url}\n\n{$error}";

        $telegramApiUrl = "https://api.telegram.org/bot{$apiToken}/sendMessage";

        try {
            $this->client->post($telegramApiUrl, [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ],
            ]);
        } catch (GuzzleException $e) {
            // Fehler beim Senden der Benachrichtigung, aber nicht kritisch
            error_log("Fehler beim Senden der Telegram-Benachrichtigung: {$e->getMessage()}");
        }
    }
}
