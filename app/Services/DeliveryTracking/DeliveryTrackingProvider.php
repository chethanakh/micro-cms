<?php

namespace App\Services\DeliveryTracking;

interface DeliveryTrackingProvider
{
    public function track(string $trackingId): ?DeliveryTrackingInfo;
}
