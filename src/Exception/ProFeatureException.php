<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class ProFeatureException extends TrackspireException
{
    public function __construct(
        string $message = 'This feature requires a Pro subscription',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, StatusCodeInterface::STATUS_FORBIDDEN, $previous);
    }
}
