<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Common;

class SortOrder
{
    private const string DIRECTION_ASC  = 'ASC';
    private const string DIRECTION_DESC = 'DESC';

    private function __construct(
        public readonly string $column,
        public readonly string $direction,
    ) {
        if (!preg_match('/^[a-zA-Z_]+$/', $column)) {
            throw new \InvalidArgumentException("Invalid sort column: $column");
        }
        if (!in_array($direction, [self::DIRECTION_ASC, self::DIRECTION_DESC], true)) {
            throw new \InvalidArgumentException("Invalid sort direction: $direction");
        }
    }

    public static function from(
        array $queryParams,
        array $allowedColumns,
        string $defaultColumn,
        string $defaultDirection = self::DIRECTION_DESC,
    ): self {
        $column    = $queryParams['sortBy'] ?? $defaultColumn;
        $direction = strtoupper($queryParams['sortDir'] ?? $defaultDirection);

        if (!in_array($column, $allowedColumns, true)) {
            $column = $defaultColumn;
        }
        if (!in_array($direction, [self::DIRECTION_ASC, self::DIRECTION_DESC], true)) {
            $direction = $defaultDirection;
        }

        return new self($column, $direction);
    }

    public function toSql(): string
    {
        return "ORDER BY {$this->column} {$this->direction}";
    }
}
