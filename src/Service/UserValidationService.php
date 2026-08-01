<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Service;

use Trackspire\CommonModule\Exception\UserAlreadyExistsException;
use Trackspire\CommonModule\Repository\UserRepository;
use Trackspire\CommonModule\Value\User\UserEmail;
use Trackspire\CommonModule\Value\User\UserId;

class UserValidationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function ensureUserDoesNotExists(
        UserEmail $email,
        ?UserId $excludeUserId = null,
    ): void {
        if ($this->userRepository->findByEmail($email, $excludeUserId)) {
            throw new UserAlreadyExistsException('User already exists with this email');
        }
    }
}
