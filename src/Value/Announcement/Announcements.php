<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Announcement;

use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

class Announcements implements IteratorAggregate, Countable, JsonSerializable
{
    /** @param array<int, Announcement> $items */
    private function __construct(
        private readonly array $items,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function fromDatabaseRows(array $rows): self
    {
        return new self(array_map(
            static fn(array $row) => Announcement::fromDatabase($row),
            $rows,
        ));
    }

    public function getIterator(): Generator
    {
        yield from $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn(Announcement $announcement) => $announcement->toArray(),
            $this->items,
        );
    }
}
