<?php

declare(strict_types=1);

namespace Monitoring\Service;

use RuntimeException;

class ConfigService
{
    private array $config;

    public function __construct(private readonly string $configPath)
    {
        if (!file_exists($this->configPath)) {
            throw new RuntimeException("Konfigurationsdatei nicht gefunden: {$this->configPath}");
        }

        $this->config = require $this->configPath;

        if (!is_array($this->config)) {
            throw new RuntimeException('Konfigurationsdatei muss ein Array zurückgeben');
        }

        $this->validate();
    }

    private function validate(): void
    {
        if (!isset($this->config['telegram']['api_token'])) {
            throw new RuntimeException('Telegram API Token fehlt in der Konfiguration');
        }

        if (!isset($this->config['websites']) || !is_array($this->config['websites'])) {
            throw new RuntimeException('Websites-Liste fehlt oder ist ungültig');
        }

        foreach ($this->config['websites'] as $index => $website) {
            if (!isset($website['url'])) {
                throw new RuntimeException("URL fehlt bei Website-Index {$index}");
            }
            if (!isset($website['timeout_seconds'])) {
                throw new RuntimeException("timeout_seconds fehlt bei Website-Index {$index}");
            }
            if (!isset($website['contains_string'])) {
                throw new RuntimeException("contains_string fehlt bei Website-Index {$index}");
            }
            if (!isset($website['telegram_chat_id'])) {
                throw new RuntimeException("telegram_chat_id fehlt bei Website-Index {$index}");
            }
        }
    }

    public function getTelegramApiToken(): string
    {
        return $this->config['telegram']['api_token'];
    }

    public function getWebsites(): array
    {
        return $this->config['websites'];
    }
}
