<?php

declare(strict_types=1);

namespace Monitoring\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class WebsiteChecker
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Prüft eine Website auf Verfügbarkeit, HTTP-Status und Inhalt.
     *
     * @return array{success: bool, error: string|null}
     */
    public function check(string $url, int $timeoutSeconds, string $containsString): array
    {
        try {
            // HTTP-Request mit Timeout
            $response = $this->client->get($url, [
                'timeout' => $timeoutSeconds,
                'connect_timeout' => $timeoutSeconds,
            ]);

            // HTTP-Statuscode prüfen
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                return [
                    'success' => false,
                    'error' => "HTTP-Statuscode war {$statusCode}, erwartet 200",
                ];
            }

            // Inhalt prüfen
            $body = (string) $response->getBody();
            if (!str_contains($body, $containsString)) {
                return [
                    'success' => false,
                    'error' => "Erwarteter String '{$containsString}' nicht im HTML-Body gefunden",
                ];
            }

            return ['success' => true, 'error' => null];
        } catch (RequestException $e) {
            // Timeout oder Connection Error
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                return [
                    'success' => false,
                    'error' => "HTTP-Fehler: Statuscode {$statusCode}",
                ];
            }

            return [
                'success' => false,
                'error' => "Timeout oder Verbindungsfehler: {$e->getMessage()}",
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => "Unerwarteter Fehler: {$e->getMessage()}",
            ];
        }
    }
}
