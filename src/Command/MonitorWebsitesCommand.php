<?php

declare(strict_types=1);

namespace Monitoring\Command;

use Monitoring\Service\AlarmService;
use Monitoring\Service\ConfigService;
use Monitoring\Service\LockService;
use Monitoring\Service\WebsiteChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'monitor:websites',
    description: 'Überwacht konfigurierte Websites und sendet Benachrichtigungen bei Fehlern'
)]
class MonitorWebsitesCommand extends Command
{
    public function __construct(
        private readonly ConfigService $configService,
        private readonly WebsiteChecker $websiteChecker,
        private readonly LockService $lockService,
        private readonly AlarmService $alarmService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $websites = $this->configService->getWebsites();
        $telegramApiToken = $this->configService->getTelegramApiToken();

        $output->writeln('<info>Starte Website-Monitoring...</info>');

        foreach ($websites as $website) {
            $url = $website['url'];
            $timeoutSeconds = $website['timeout_seconds'];
            $containsString = $website['contains_string'];
            $chatId = $website['telegram_chat_id'];

            $output->writeln("Prüfe: {$url}");

            // Website prüfen
            $result = $this->websiteChecker->check($url, $timeoutSeconds, $containsString);

            if ($result['success']) {
                $output->writeln("  <comment>✓ OK</comment>");
                continue;
            }

            // Fehler aufgetreten
            $error = $result['error'];
            $output->writeln("  <error>✗ Fehler: {$error}</error>");

            // Lock prüfen
            if ($this->lockService->isLocked($url)) {
                $output->writeln('  <comment>Lock aktiv, keine Benachrichtigung gesendet</comment>');
                continue;
            }

            // Benachrichtigung senden
            $this->alarmService->sendTelegramNotification(
                $telegramApiToken,
                $chatId,
                $url,
                $error
            );

            $output->writeln('  <info>Telegram-Benachrichtigung gesendet</info>');

            // Lock erstellen
            $this->lockService->createLock($url);
        }

        $output->writeln('<info>Monitoring abgeschlossen</info>');

        return Command::SUCCESS;
    }
}
