<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\EarlyAccess;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class EarlyAccessCodeCollection implements IteratorAggregate, Countable, JsonSerializable
{
    /** @param array<int, EarlyAccessCode> $items */
    private function __construct(
        private readonly array $items,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function from(EarlyAccessCode ...$codes): self
    {
        return new self(array_values($codes));
    }

    public static function fromDatabaseRows(array $rows): self
    {
        return new self(array_map(
            static fn(array $row) => EarlyAccessCode::fromDatabase($row),
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

    public function jsonSerialize(): array
    {
        return array_map(
            static fn(EarlyAccessCode $code) => $code->toArray(),
            $this->items,
        );
    }
}
