<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class NotFoundException extends TrackspireException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, StatusCodeInterface::STATUS_NOT_FOUND, $previous);
    }
}
