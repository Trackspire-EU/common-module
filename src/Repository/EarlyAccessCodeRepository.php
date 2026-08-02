<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\EarlyAccess\EarlyAccessCode;
use Trackspire\CommonModule\Value\EarlyAccess\EarlyAccessCodeCollection;

class EarlyAccessCodeRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function create(EarlyAccessCode $code): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO early_access_codes'
                . ' (id, code, max_uses, uses_count, email, note, expires_at, created_by, created_at)'
                . ' VALUES (:id, :code, :max_uses, 0, :email, :note, :expires_at, :created_by, NOW())',
            );

            $stmt->execute([
                'id'         => $code->getId()->asString(),
                'code'       => $code->getCode(),
                'max_uses'   => $code->getMaxUses(),
                'email'      => $code->getEmail(),
                'note'       => $code->getNote(),
                'expires_at' => $code->getExpiresAt()?->format('Y-m-d H:i:s'),
                'created_by' => $code->getCreatedBy(),
            ]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to create early access code',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function findByCode(string $rawCode): ?EarlyAccessCode
    {
        $normalized = strtoupper(str_replace('-', '', $rawCode));

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, code, max_uses, uses_count, email, note, expires_at, created_by, created_at
                 FROM early_access_codes WHERE code = :code',
            );
            $stmt->execute(['code' => $normalized]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to find early access code',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        return $row !== false ? EarlyAccessCode::fromDatabase($row) : null;
    }

    public function findById(string $id): ?EarlyAccessCode
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, code, max_uses, uses_count, email, note, expires_at, created_by, created_at
                 FROM early_access_codes WHERE id = :id',
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to find early access code',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        return $row !== false ? EarlyAccessCode::fromDatabase($row) : null;
    }

    public function incrementUses(string $id): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE early_access_codes SET uses_count = uses_count + 1 WHERE id = :id',
            );
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to increment early access code uses',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function deleteById(string $id): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM early_access_codes WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to delete early access code',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function loadAll(): EarlyAccessCodeCollection
    {
        try {
            $stmt = $this->pdo->query(
                'SELECT id, code, max_uses, uses_count, email, note, expires_at, created_by, created_at
                 FROM early_access_codes ORDER BY created_at DESC',
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load early access codes',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        return EarlyAccessCodeCollection::fromDatabaseRows($rows);
    }
}