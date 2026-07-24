<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\App\Factory;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Trackspire\CommonModule\App\Handler\TelegramHandler;
use Trackspire\CommonModule\Repository\EnvironmentRepository;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        /** @var EnvironmentRepository $envRepository */
        $envRepository = $container->get(EnvironmentRepository::class);

        $logFilePath = $envRepository->get('LOG_FILE_PATH', '/app/logs/trackspire-log');
        $logLevel = $envRepository->get('LOG_LEVEL', LogLevel::DEBUG);

        $logger = new Logger('trackspire-backend-api');

        $rotatingHandler = new RotatingFileHandler($logFilePath, 3, $logLevel);
        $rotatingHandler->setFormatter(new JsonFormatter());
        $logger->pushHandler($rotatingHandler);

        $telegramBotToken = $envRepository->get('TELEGRAM_BOT_TOKEN', '');
        $telegramChatId = $envRepository->get('TELEGRAM_CHAT_ID', '');

        if ($telegramBotToken !== '' && $telegramChatId !== '') {
            $logger->pushHandler(new TelegramHandler($telegramBotToken, $telegramChatId));
        }

        return $logger;
    }
}
