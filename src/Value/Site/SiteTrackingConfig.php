<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

class SiteTrackingConfig
{
    private function __construct(
        public readonly bool $blockBots,
        public readonly bool $webVitals,
        public readonly bool $trackErrors,
        public readonly bool $trackOutbound,
        public readonly bool $trackUrlParams,
        public readonly bool $trackInitial,
        public readonly bool $trackSpaNavigation,
        public readonly bool $trackIp,
        public readonly bool $trackButtonClicks,
        public readonly bool $trackCopy,
        public readonly bool $trackFormInteractions,
        public readonly bool $saltUserIds,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            blockBots:             true,
            webVitals:             false,
            trackErrors:           false,
            trackOutbound:         true,
            trackUrlParams:        true,
            trackInitial:          true,
            trackSpaNavigation:    true,
            trackIp:               false,
            trackButtonClicks:     false,
            trackCopy:             false,
            trackFormInteractions: false,
            saltUserIds:           false,
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            blockBots:             (bool) $row['block_bots'],
            webVitals:             (bool) $row['web_vitals'],
            trackErrors:           (bool) $row['track_errors'],
            trackOutbound:         (bool) $row['track_outbound'],
            trackUrlParams:        (bool) $row['track_url_params'],
            trackInitial:          (bool) $row['track_initial'],
            trackSpaNavigation:    (bool) $row['track_spa_navigation'],
            trackIp:               (bool) $row['track_ip'],
            trackButtonClicks:     (bool) $row['track_button_clicks'],
            trackCopy:             (bool) $row['track_copy'],
            trackFormInteractions: (bool) $row['track_form_interactions'],
            saltUserIds:           (bool) ($row['salt_user_ids'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'blockBots'             => $this->blockBots,
            'webVitals'             => $this->webVitals,
            'trackErrors'           => $this->trackErrors,
            'trackOutbound'         => $this->trackOutbound,
            'trackUrlParams'        => $this->trackUrlParams,
            'trackInitial'          => $this->trackInitial,
            'trackSpaNavigation'    => $this->trackSpaNavigation,
            'trackIp'               => $this->trackIp,
            'trackButtonClicks'     => $this->trackButtonClicks,
            'trackCopy'             => $this->trackCopy,
            'trackFormInteractions' => $this->trackFormInteractions,
            'saltUserIds'           => $this->saltUserIds,
        ];
    }

    public function toDatabaseArray(): array
    {
        return [
            'block_bots'              => (int) $this->blockBots,
            'web_vitals'              => (int) $this->webVitals,
            'track_errors'            => (int) $this->trackErrors,
            'track_outbound'          => (int) $this->trackOutbound,
            'track_url_params'        => (int) $this->trackUrlParams,
            'track_initial'           => (int) $this->trackInitial,
            'track_spa_navigation'    => (int) $this->trackSpaNavigation,
            'track_ip'                => (int) $this->trackIp,
            'track_button_clicks'     => (int) $this->trackButtonClicks,
            'track_copy'              => (int) $this->trackCopy,
            'track_form_interactions' => (int) $this->trackFormInteractions,
            'salt_user_ids'           => (int) $this->saltUserIds,
        ];
    }
}
