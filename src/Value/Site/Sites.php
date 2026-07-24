<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

class Sites implements IteratorAggregate, JsonSerializable, Countable
{
    private readonly array $sites;

    private function __construct(Site ...$sites)
    {
        $this->sites = $sites;
    }

    public static function from(Site ...$sites): self
    {
        return new self(...$sites);
    }

    public function getIterator(): Generator
    {
        yield from $this->sites;
    }

    public function count(): int
    {
        return count($this->sites);
    }

    public function toArray(): array
    {
        return array_map(static fn (Site $site) => $site->toArray(), $this->sites);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
