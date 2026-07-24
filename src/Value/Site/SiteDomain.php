<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

use Fig\Http\Message\StatusCodeInterface;
use Trackspire\CommonModule\Exception\ValueException;

class SiteDomain
{
    private function __construct(
        private readonly string $domain,
    ) {
    }

    public static function from(string $domain): self
    {
        $domain = strtolower(trim($domain));

        if ($domain === '') {
            throw new ValueException('Domain cannot be empty.', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (str_contains($domain, '://')) {
            throw new ValueException('Domain must not include a protocol.', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (str_contains($domain, '/')) {
            throw new ValueException('Domain must not include a path.', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
            throw new ValueException("'{$domain}' is not a valid domain.", StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        return new self($domain);
    }

    public function asString(): string
    {
        return $this->domain;
    }
}
