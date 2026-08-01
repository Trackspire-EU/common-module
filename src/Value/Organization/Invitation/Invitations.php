<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization\Invitation;

use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

class Invitations implements IteratorAggregate, JsonSerializable, Countable
{
    private readonly array $invitations;

    private function __construct(Invitation ...$invitations)
    {
        $this->invitations = $invitations;
    }

    public static function from(Invitation ...$invitations): self
    {
        return new self(...$invitations);
    }

    public function getIterator(): Generator
    {
        yield from $this->invitations;
    }

    public function count(): int
    {
        return count($this->invitations);
    }

    public function toArray(): array
    {
        return array_map(static fn (Invitation $invitation) => $invitation->toArray(), $this->invitations);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
