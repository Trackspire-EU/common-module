<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Announcement;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\ValidationException;

enum AnnouncementType: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Success = 'success';

    public static function fromString(string $type): self
    {
        $case = self::tryFrom($type);
        if ($case === null) {
            throw new ValidationException(
                sprintf('Unknown announcement type "%s"', $type),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            );
        }
        return $case;
    }
}
