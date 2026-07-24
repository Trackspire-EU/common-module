<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Exception;
use Throwable;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
class TrackspireException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
        private readonly array $context = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function hasContext(): bool
    {
        return !empty($this->context);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
