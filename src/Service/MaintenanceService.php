<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Service;

use Redis;

class MaintenanceService
{
    private const string REDIS_KEY = 'maintenance';

    public function __construct(
        private readonly Redis $redis,
    ) {
    }

    public function getConfig(): array
    {
        return [
            'dashboardDisabled' => $this->redis->hGet(self::REDIS_KEY, 'dashboardDisabled') === '1',
            'trackingDisabled' => $this->redis->hGet(self::REDIS_KEY, 'trackingDisabled') === '1',
            'message' => $this->redis->hGet(self::REDIS_KEY, 'message') ?: '',
        ];
    }

    public function getPublicStatus(): array
    {
        return [
            'dashboardDisabled' => $this->redis->hGet(self::REDIS_KEY, 'dashboardDisabled') === '1',
            'message' => $this->redis->hGet(self::REDIS_KEY, 'message') ?: '',
        ];
    }

    public function isTrackingDisabled(): bool
    {
        return $this->redis->hGet(self::REDIS_KEY, 'trackingDisabled') === '1';
    }

    public function update(bool $dashboardDisabled, bool $trackingDisabled, string $message): array
    {
        $this->redis->hMSet(self::REDIS_KEY, [
            'dashboardDisabled' => $dashboardDisabled ? '1' : '0',
            'trackingDisabled' => $trackingDisabled ? '1' : '0',
            'message' => $message,
        ]);

        return [
            'dashboardDisabled' => $dashboardDisabled,
            'trackingDisabled' => $trackingDisabled,
            'message' => $message,
        ];
    }
}
