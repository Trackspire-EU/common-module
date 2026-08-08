<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Changelog;

use Countable;
use DateTimeImmutable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

class ChangelogEntries implements IteratorAggregate, Countable, JsonSerializable
{
    /** @param array<int, ChangelogEntry> $items */
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
            static fn(array $row) => ChangelogEntry::fromDatabase($row),
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

    public function newestPublishedAt(): ?DateTimeImmutable
    {
        if ($this->items === []) {
            return null;
        }

        return $this->items[0]->getPublishedAt();
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn(ChangelogEntry $entry) => $entry->toArray(),
            $this->items,
        );
    }
}
