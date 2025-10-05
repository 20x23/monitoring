<?php

declare(strict_types=1);

return [
    'telegram' => [
        'api_token' => 'DEIN_TELEGRAM_BOT_API_TOKEN', // Ersetze dies mit deinem echten Telegram Bot Token
    ],

    'websites' => [
        [
            'url' => 'https://example.com',
            'timeout_seconds' => 10,
            'contains_string' => 'Example Domain',
            'telegram_chat_id' => 'EMPFÄNGER_CHAT_ID_1', // Ersetze dies mit der Chat-ID des Empfängers
        ],
        [
            'url' => 'https://httpbin.org/status/200',
            'timeout_seconds' => 5,
            'contains_string' => '',
            'telegram_chat_id' => 'EMPFÄNGER_CHAT_ID_2', // Ersetze dies mit der Chat-ID des Empfängers
        ],
    ],
];
