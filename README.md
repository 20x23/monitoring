# Website Monitor

Einfaches PHP-basiertes Website-Monitoring-Tool mit Telegram-Benachrichtigungen.

## Installation

```bash
docker-compose up -d
docker-compose exec app composer install
```

## Konfiguration

Bearbeite `config/websites.php` und setze:

- `telegram.api_token`: Dein Telegram Bot API Token
- `websites`: Array der zu überwachenden Websites mit:
  - `url`: Die zu prüfende URL
  - `timeout_seconds`: Timeout in Sekunden
  - `contains_string`: Erwarteter String im HTML-Body
  - `telegram_chat_id`: Chat-ID für Benachrichtigungen

## Verwendung

```bash
docker-compose exec app php bin/console monitor:websites
```

## Cron-Job einrichten

Füge in deinem Host-System oder Container einen Cron-Job hinzu:

```cron
*/5 * * * * cd /pfad/zum/projekt && docker-compose exec -T app php bin/console monitor:websites
```

## Funktionsweise

1. Prüft jede konfigurierte Website auf:
   - Erreichbarkeit innerhalb des Timeouts
   - HTTP-Statuscode 200
   - Vorhandensein des erwarteten Strings im HTML-Body

2. Bei Fehlern:
   - Prüft Lock-Datei (max. 30 Minuten alt)
   - Sendet Telegram-Benachrichtigung (falls kein Lock)
   - Erstellt Lock-Datei
