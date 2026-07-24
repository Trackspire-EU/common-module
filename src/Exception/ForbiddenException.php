<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class ForbiddenException extends TrackspireException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, StatusCodeInterface::STATUS_FORBIDDEN, $previous);
    }
}
