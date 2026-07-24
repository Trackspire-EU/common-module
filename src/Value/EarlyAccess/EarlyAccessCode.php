<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\EarlyAccess;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\User\UserEmail;
use Random\RandomException;

class EarlyAccessCode
{
    private function __construct(
        private readonly EarlyAccessCodeId $id,
        private readonly string $code,
        private readonly int $maxUses,
        private readonly int $usesCount,
        private readonly ?UserEmail $email,
        private readonly ?string $note,
        private readonly ?DateTimeImmutable $expiresAt,
        private readonly string $createdBy,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * @throws RandomException
     */
    public static function create(
        string $createdBy,
        int $maxUses,
        ?UserEmail $email = null,
        ?string $note = null,
        ?DateTimeImmutable $expiresAt = null,
    ): self {
        return new self(
            EarlyAccessCodeId::fromRandom(),
            strtoupper(bin2hex(random_bytes(8))),
            $maxUses,
            0,
            $email,
            $note !== '' ? $note : null,
            $expiresAt,
            $createdBy,
            new DateTimeImmutable(),
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            EarlyAccessCodeId::from($row['id']),
            $row['code'],
            (int) $row['max_uses'],
            (int) $row['uses_count'],
            isset($row['email']) ? UserEmail::from($row['email']) : null,
            $row['note'] ?? null,
            isset($row['expires_at']) ? new DateTimeImmutable($row['expires_at']) : null,
            $row['created_by'],
            new DateTimeImmutable($row['created_at']),
        );
    }

    public function getId(): EarlyAccessCodeId
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMaxUses(): int
    {
        return $this->maxUses;
    }

    public function getUsesCount(): int
    {
        return $this->usesCount;
    }

    public function getEmail(): ?UserEmail
    {
        return $this->email;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function isExhausted(): bool
    {
        return $this->usesCount >= $this->maxUses;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt < new DateTimeImmutable();
    }

    /** Formatted as XXXX-XXXX-XXXX-XXXX for display */
    public function getFormattedCode(): string
    {
        return implode('-', str_split($this->code, 4));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->asString(),
            'code' => $this->getFormattedCode(),
            'max_uses' => $this->maxUses,
            'uses_count' => $this->usesCount,
            'email' => $this->email?->asString(),
            'note' => $this->note,
            'expires_at' => $this->expiresAt?->format(DATE_ATOM),
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
