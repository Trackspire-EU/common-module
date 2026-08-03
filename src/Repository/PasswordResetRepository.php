<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Ramsey\Uuid\Uuid;
use Redis;

class PasswordResetRepository
{
    private const string KEY_PREFIX = 'password_reset:';
    private const int TTL_SECONDS = 3600;

    public function __construct(
        private readonly Redis $redis,
    ) {
    }

    public function createToken(string $userId): string
    {
        $token = Uuid::uuid4()->toString();
        $this->redis->setex(self::KEY_PREFIX . $token, self::TTL_SECONDS, $userId);
        return $token;
    }

    public function getUserIdByToken(string $token): ?string
    {
        $userId = $this->redis->get(self::KEY_PREFIX . $token);
        return $userId !== false ? $userId : null;
    }

    public function deleteToken(string $token): void
    {
        $this->redis->del(self::KEY_PREFIX . $token);
    }
}
