<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class Organizations implements IteratorAggregate, Countable, JsonSerializable
{
    /** @param array<int, OrganizationWithRole> $items */
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
            static fn(array $row) => OrganizationWithRole::fromDatabase($row),
            $rows,
        ));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function first(): ?OrganizationWithRole
    {
        return $this->items[0] ?? null;
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn(OrganizationWithRole $org) => $org->toArray(),
            $this->items,
        );
    }
}
