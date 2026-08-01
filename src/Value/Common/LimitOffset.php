<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Common;

class LimitOffset
{
    private function __construct(
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public static function from(int|string|null $limit, int|string|null $offset): self
    {
        return new self(
            max(1, min(500, (int) ($limit ?? 100))),
            max(0, (int) ($offset ?? 0)),
        );
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
