<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Monitoring\Service\AlarmService;
use Monitoring\Service\ConfigService;
use Monitoring\Service\LockService;
use Monitoring\Service\WebsiteChecker;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    Client::class => function () {
        return new Client();
    },

    ConfigService::class => function () {
        return new ConfigService(__DIR__ . '/../config/websites.php');
    },

    LockService::class => function () {
        return new LockService(__DIR__ . '/../var/locks');
    },

    WebsiteChecker::class => function ($container) {
        return new WebsiteChecker($container->get(Client::class));
    },

    AlarmService::class => function ($container) {
        return new AlarmService($container->get(Client::class));
    },
]);

return $containerBuilder->build();
