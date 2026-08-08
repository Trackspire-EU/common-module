<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Changelog;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\ValidationException;

enum ChangelogCategory: string
{
    case Feature = 'feature';
    case Fix = 'fix';
    case Improvement = 'improvement';
    case Breaking = 'breaking';

    public static function fromString(string $category): self
    {
        $case = self::tryFrom($category);
        if ($case === null) {
            throw new ValidationException(
                sprintf('Unknown changelog category "%s"', $category),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            );
        }
        return $case;
    }
}
