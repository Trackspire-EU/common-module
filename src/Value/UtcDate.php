<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Trackspire\CommonModule\Exception\InvalidDateFormatException;
use Trackspire\CommonModule\Exception\RequestValidationException;

final class UtcDate
{
    public const string FORMAT_HUMAN = 'Y-m-d H:i:s';

    public static function from(string $format, string $value, ?DateTimeZone $inputTimeZone = null): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat($format, $value, $inputTimeZone);

        if ($date === false) {
            throw new InvalidDateFormatException($value, $format);
        }

        return $date->setTimezone(new DateTimeZone('UTC'));
    }

    public static function parseTimeZone(string $value): DateTimeZone
    {
        try {
            return new DateTimeZone($value);
        } catch (\Throwable $e) {
            throw new RequestValidationException(sprintf('Invalid timezone: "%s"', $value), previous: $e);
        }
    }

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    public static function parse(string $value, ?DateTimeZone $inputTimeZone = null): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value, $inputTimeZone)->setTimezone(new DateTimeZone('UTC'));
        } catch (Exception $e) {
            throw new InvalidDateFormatException($value, null, $e);
        }
    }

    public static function inZone(string $value, DateTimeZone $timeZone): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value, $timeZone);
        } catch (Exception $e) {
            throw new InvalidDateFormatException($value, null, $e);
        }
    }
}
