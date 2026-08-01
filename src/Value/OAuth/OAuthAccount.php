<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\OAuth;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\User\UserId;
use Trackspire\CommonModule\Value\UtcDate;

class OAuthAccount
{
    private function __construct(
        private readonly ?int $oauthId,
        private readonly UserId $userId,
        private readonly OAuthProvider $provider,
        private readonly string $providerUserId,
        private ?string $accessToken,
        private ?string $refreshToken,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        UserId $userId,
        OAuthProvider $provider,
        string $providerUserId,
        ?string $accessToken,
        ?string $refreshToken,
    ): self {
        return new self(
            null,
            $userId,
            $provider,
            $providerUserId,
            $accessToken,
            $refreshToken,
            UtcDate::now(),
            null,
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            (int) $row['oauth_id'],
            UserId::from($row['user_id']),
            OAuthProvider::from($row['provider']),
            $row['provider_user_id'],
            $row['access_token'],
            $row['refresh_token'],
            new DateTimeImmutable($row['created_at']),
            $row['updated_at'] !== null ? new DateTimeImmutable($row['updated_at']) : null,
        );
    }

    public function getOauthId(): ?int
    {
        return $this->oauthId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getProvider(): OAuthProvider
    {
        return $this->provider;
    }

    public function getProviderUserId(): string
    {
        return $this->providerUserId;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function updateTokens(?string $accessToken, ?string $refreshToken): void
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}