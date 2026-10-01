<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],

                  // Base de données (MAMP)
                'db' => [
                    'host'     => $_ENV['DB_HOST'] ?? '127.0.0.1',
                    'port'     => (int) ($_ENV['DB_PORT'] ?? 8889),   // MAMP (WAMP : 3306)
                    'database' => $_ENV['DB_NAME'] ?? 'music',
                    'username' => $_ENV['DB_USER'] ?? 'root',
                    'password' => $_ENV['DB_PASS'] ?? '',             // MAMP : root (WAMP : '')
                    'charset'  => 'utf8mb4',
                    'flags'    => [
                        PDO::ATTR_PERSISTENT         => false,
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES   => true,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ],
                ],                
            ]);
        }
    ]);
};
