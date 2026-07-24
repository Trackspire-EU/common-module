<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\User;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\UtcDate;

class User
{
    private function __construct(
        private readonly UserId $userId,
        private UserEmail $email,
        private ?UserPassword $password,
        private bool $isActive,
        private ?DateTimeImmutable $lastActive,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        string $email,
        string $password,
        bool $isActive = true,
    ): self {
        return new self(
            UserId::fromRandom(),
            UserEmail::from($email),
            UserPassword::fromPlain($password),
            $isActive,
            null,
            UtcDate::now(),
            null,
        );
    }

    public static function createForOAuth(string $email): self
    {
        return new self(
            UserId::fromRandom(),
            UserEmail::from($email),
            null,
            true,
            null,
            UtcDate::now(),
            null,
        );
    }

    public static function fromDatabase(array $row): self
    {
        $updatedAt = $row['updated_at'] === null ? null : new DateTimeImmutable($row['updated_at']);
        $lastActive = $row['last_active'] === null ? null : new DateTimeImmutable($row['last_active']);
        $password = $row['password'] !== null ? UserPassword::fromHash($row['password']) : null;

        return new self(
            UserId::from($row['user_id']),
            UserEmail::from($row['email']),
            $password,
            (bool) $row['is_active'],
            $lastActive,
            new DateTimeImmutable($row['created_at']),
            $updatedAt,
        );
    }

    public function toArray(): array
    {
        return  [
            'userId' => $this->userId->asString(),
            'email' => $this->email->asString(),
            'isActive' => $this->isActive,
            'lastActive' => $this->lastActive?->format('Y-m-d H:i:s'),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getPassword(): ?UserPassword
    {
        return $this->password;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getLastActive(): ?DateTimeImmutable
    {
        return $this->lastActive;
    }

    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    public function setEmail(UserEmail $email): void
    {
        $this->email = $email;
    }

    public function setPassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setLastActive(?DateTimeImmutable $lastActive): void
    {
        $this->lastActive = $lastActive;
    }
}
