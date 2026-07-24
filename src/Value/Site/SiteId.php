<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

class SiteId
{
    private function __construct(
        private readonly int $identifier,
    ) {
    }

    public static function from(int $identifier): self
    {
        return new self($identifier);
    }

    public function asInt(): int
    {
        return $this->identifier;
    }
}
