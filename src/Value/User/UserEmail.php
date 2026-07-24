<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\User;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\ValidationException;

class UserEmail
{
    private function __construct(
        private readonly string $email
    ) {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email', StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }

    public static function from(string $email): self
    {
        return new self($email);
    }

    public function asString(): string
    {
        return $this->email;
    }
}
