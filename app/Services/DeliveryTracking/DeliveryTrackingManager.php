<?php

namespace App\Services\DeliveryTracking;

use App\Enums\DeliveryServiceProvider;
use App\Services\DeliveryTracking\Providers\FaderDomesticTrackingProvider;

class DeliveryTrackingManager
{
    public function track(DeliveryServiceProvider|string|null $provider, ?string $trackingId): ?DeliveryTrackingInfo
    {
        if ($trackingId === null || trim($trackingId) === '') {
            return null;
        }

        $providerEnum = $provider instanceof DeliveryServiceProvider
            ? $provider
            : DeliveryServiceProvider::tryFrom((string) $provider);

        if (! $providerEnum) {
            return null;
        }

        return match ($providerEnum) {
            DeliveryServiceProvider::FaderDomestic => (new FaderDomesticTrackingProvider)->track($trackingId),
        };
    }
}
