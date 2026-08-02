<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Service;

use Redis;
use Trackspire\CommonModule\Exception\ValidationException;
use Trackspire\CommonModule\Repository\EarlyAccessCodeRepository;

class EarlyAccessService
{
    private const string REDIS_KEY = 'early_access:enabled';

    public function __construct(
        private readonly Redis $redis,
        private readonly EarlyAccessCodeRepository $codeRepository,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->redis->get(self::REDIS_KEY) === '1';
    }

    public function validateAndRedeem(string $rawCode, string $registrationEmail): void
    {
        $code = $this->codeRepository->findByCode($rawCode);

        if ($code === null) {
            throw new ValidationException('Invalid early access code');
        }

        if ($code->isExpired()) {
            throw new ValidationException('Early access code has expired');
        }

        if ($code->isExhausted()) {
            throw new ValidationException('Early access code has already been fully used');
        }

        if ($code->getEmail() !== null && strtolower($code->getEmail()->asString()) !== strtolower($registrationEmail)) {
            throw new ValidationException('Early access code is not valid for this email address');
        }

        $this->codeRepository->incrementUses($code->getId()->asString());
    }
}