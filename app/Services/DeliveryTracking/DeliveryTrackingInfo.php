<?php

namespace App\Services\DeliveryTracking;

class DeliveryTrackingInfo
{
    public function __construct(
        public readonly string $trackingId,
        public readonly string $status,
        public readonly ?string $location,
        public readonly ?string $lastUpdate,
        public readonly string $sourceUrl,
    ) {}
}
