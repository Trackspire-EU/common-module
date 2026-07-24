<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception\Formatter;

use Throwable;
use Trackspire\CommonModule\Exception\FormattableException;
use Trackspire\CommonModule\Util\CliUtils;

class ExceptionFormatter
{
    public function __construct(
        private readonly CliUtils $cliUtils,
    ) {
    }

    public function formatException(Throwable $throwable): void
    {
        if ($throwable instanceof FormattableException) {
            $isInteractiveErr = $this->cliUtils->isInteractiveErr();
            $this->cliUtils->writeErr(match ($isInteractiveErr) {
                    true => $throwable->formatHuman(),
                    false => $throwable->format(),
            } . PHP_EOL);

            return;
        }

        $this->cliUtils->writeErr($throwable . PHP_EOL);
    }
}
