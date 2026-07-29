<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\User\User;
use Trackspire\CommonModule\Value\User\UserEmail;
use Trackspire\CommonModule\Value\User\UserId;
use PDO;
use PDOException;

class UserRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function create(User $user): void
    {
        $sql = <<<SQL
            INSERT INTO users (user_id, email, password)
            VALUES (:userId, :email, :password)
        SQL;

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                'userId' => $user->getUserId()->asString(),
                'email' => $user->getEmail()->asString(),
                'password' => $user->getPassword()?->asString(),
            ]);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException(
                'Failed to create user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function findByEmail(UserEmail $email, ?UserId $excludeUserId = null): ?User
    {
        $sql = <<<SQL
            SELECT user_id, email, password, is_active, last_active, created_at, updated_at
            FROM users
            WHERE email = :email
        SQL;

        if ($excludeUserId !== null) {
            $sql .= ' AND user_id != :exclude_id';
        }

        try {
            $statement = $this->pdo->prepare($sql);
            $params = ['email' => $email->asString()];
            if ($excludeUserId !== null) {
                $params['exclude_id'] = $excludeUserId->asString();
            }
            $statement->execute($params);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to find user by email',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }

        return $row !== false ? User::fromDatabase($row) : null;
    }

    public function findById(UserId $userId): ?User
    {
        $sql = <<<SQL
            SELECT user_id, email, password, is_active, last_active, created_at, updated_at
            FROM users
            WHERE user_id = :user_id
        SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute(['user_id' => $userId->asString()]);

            $row = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to find user by id',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }

        return $row !== false ? User::fromDatabase($row) : null;
    }

    public function save(User $user): void
    {
        $sql = <<<SQL
            UPDATE users
            SET
                email      = :email,
                password   = :password,
                is_active  = :isActive,
                updated_at = NOW()
            WHERE user_id  = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'email'      => $user->getEmail()->asString(),
                'password'   => $user->getPassword()?->asString(),
                'isActive'   => (int) $user->isActive(),
                'userId'     => $user->getUserId()->asString(),
            ]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to save user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function updateLastActive(UserId $userId): void
    {
        $sql = 'UPDATE users SET last_active = NOW() WHERE user_id = :userId';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId->asString()]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to update last active',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function delete(UserId $userId): void
    {
        $sql = 'DELETE FROM users WHERE user_id = :userId';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId->asString()]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to delete user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
