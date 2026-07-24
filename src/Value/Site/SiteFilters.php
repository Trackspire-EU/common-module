<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

class SiteFilters
{
    private function __construct(
        public readonly bool $public,
        public readonly array $excludedIps,
        public readonly array $excludedCountries,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            public:            false,
            excludedIps:       [],
            excludedCountries: [],
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            public:            (bool) $row['public'],
            excludedIps:       json_decode($row['excluded_ips'], true, 512, JSON_THROW_ON_ERROR) ?? [],
            excludedCountries: json_decode($row['excluded_countries'], true, 512, JSON_THROW_ON_ERROR) ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'public'            => $this->public,
            'excludedIps'       => $this->excludedIps,
            'excludedCountries' => $this->excludedCountries,
        ];
    }

    public function toDatabaseArray(): array
    {
        return [
            'public'             => (int) $this->public,
            'excluded_ips'       => json_encode($this->excludedIps, JSON_THROW_ON_ERROR),
            'excluded_countries' => json_encode($this->excludedCountries, JSON_THROW_ON_ERROR),
        ];
    }
}
