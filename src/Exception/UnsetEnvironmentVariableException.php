<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class UnsetEnvironmentVariableException extends TrackspireException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $previous);
    }
}
