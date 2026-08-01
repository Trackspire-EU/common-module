<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\OAuth;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\ValidationException;

enum OAuthProvider: string
{
    case Google = 'google';
    case Github = 'github';

    public static function fromString(string $provider): self
    {
        $case = self::tryFrom($provider);
        if ($case === null) {
            throw new ValidationException(
                sprintf('Unknown OAuth provider "%s"', $provider),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            );
        }
        return $case;
    }
}