<?php

declare(strict_types=1);

namespace Monitoring\Service;

use RuntimeException;

class LockService
{
    private const LOCK_DURATION_SECONDS = 1800; // 30 Minuten

    public function __construct(private readonly string $lockDirectory)
    {
        if (!is_dir($this->lockDirectory)) {
            throw new RuntimeException("Lock-Verzeichnis existiert nicht: {$this->lockDirectory}");
        }

        if (!is_writable($this->lockDirectory)) {
            throw new RuntimeException("Lock-Verzeichnis ist nicht beschreibbar: {$this->lockDirectory}");
        }
    }

    /**
     * Prüft, ob für die URL ein Lock aktiv ist (jünger als 30 Minuten).
     */
    public function isLocked(string $url): bool
    {
        $lockFile = $this->getLockFilePath($url);

        if (!file_exists($lockFile)) {
            return false;
        }

        $timestamp = (int) file_get_contents($lockFile);
        $age = time() - $timestamp;

        return $age < self::LOCK_DURATION_SECONDS;
    }

    /**
     * Erstellt oder aktualisiert die Lock-Datei für die URL.
     */
    public function createLock(string $url): void
    {
        $lockFile = $this->getLockFilePath($url);
        file_put_contents($lockFile, (string) time());
    }

    private function getLockFilePath(string $url): string
    {
        $hash = md5($url);
        return $this->lockDirectory . '/' . $hash . '.lock';
    }
}
