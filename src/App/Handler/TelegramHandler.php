<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\App\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;

class TelegramHandler extends AbstractProcessingHandler
{
    private const LEVEL_EMOJIS = [
        'ERROR'     => '🔴',
        'CRITICAL'  => '🚨',
        'ALERT'     => '🆘',
        'EMERGENCY' => '💀',
    ];

    public function __construct(
        private readonly string $botToken,
        private readonly string $chatId,
    ) {
        parent::__construct(Level::Error);
    }

    protected function write(LogRecord $record): void
    {
        $message = $this->formatTelegramMessage($record);

        try {
            $this->sendMessage($message);
        } catch (Throwable $ignored) {
            // Fail silently — logging must never break the app
        }
    }

    private function formatTelegramMessage(LogRecord $record): string
    {
        $levelName = $record->level->getName();
        $emoji = self::LEVEL_EMOJIS[$levelName] ?? '⚠️';
        $time = $record->datetime->format('Y-m-d H:i:s');
        $message = htmlspecialchars($record->message, ENT_QUOTES);

        $lines = [
            "{$emoji} <b>Trackspire Backend-API — {$levelName}</b>",
            '',
            "<b>Message:</b> {$message}",
            "<b>Time:</b> {$time}",
        ];

        $context = $record->context;

        if (isset($context['exception']) && $context['exception'] instanceof Throwable) {
            $e = $context['exception'];
            $lines[] = '';
            $lines[] = '<b>Exception:</b> <code>' . htmlspecialchars($e::class, ENT_QUOTES) . '</code>';
            $lines[] = '<b>File:</b> <code>'
                . htmlspecialchars($e->getFile(), ENT_QUOTES) . ':' . $e->getLine() . '</code>';
            unset($context['exception']);
        }

        if (isset($context['method'], $context['uri'])) {
            $lines[] = '<b>Request:</b> ' . htmlspecialchars($context['method'] . ' ' . $context['uri'], ENT_QUOTES);
            unset($context['method'], $context['uri'], $context['query']);
        }

        $contextLines = $this->formatContext($context);
        if ($contextLines !== '') {
            $lines[] = '';
            $lines[] = '<b>Context:</b>';
            $lines[] = $contextLines;
        }

        return implode("\n", $lines);
    }

    private function formatContext(array $context): string
    {
        if ($context === []) {
            return '';
        }

        $lines = [];
        foreach ($context as $key => $value) {
            $formattedValue = match (true) {
                is_array($value)  => json_encode(
                    $value,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                is_bool($value)   => $value ? 'true' : 'false',
                is_null($value)   => 'null',
                default           => (string) $value,
            };

            $lines[] = '• <code>' . htmlspecialchars($key, ENT_QUOTES) . '</code>: '
                . htmlspecialchars($formattedValue, ENT_QUOTES);
        }

        return implode("\n", $lines);
    }

    private function sendMessage(string $text): void
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $payload = json_encode([
            'chat_id'    => $this->chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ], JSON_THROW_ON_ERROR);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 3,
            ],
        ]);

        file_get_contents($url, false, $context);
    }
}
