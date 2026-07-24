<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value;

use Random\RandomException;

class AbstractId
{
    protected const int LENGTH = 8;
    private const string CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    private function __construct(
        private readonly string $identifier,
    ) {
    }

    /**
     * @throws RandomException
     */
    public static function fromRandom(): static
    {
        $result = '';
        $randomBytes = random_bytes(static::LENGTH);
        $charsetLength = strlen(self::CHARSET);

        for ($i = 0; $i < static::LENGTH; $i++) {
            $randomIndex = ord($randomBytes[$i]) % $charsetLength;
            $result .= self::CHARSET[$randomIndex];
        }

        return new static($result);
    }

    public static function from(string $identifier): static
    {
        return new static($identifier);
    }

    public function asString(): string
    {
        return $this->identifier;
    }
}
