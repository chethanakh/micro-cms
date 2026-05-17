<?php

namespace App\Services\DeliveryTracking\Providers;

use App\Services\DeliveryTracking\DeliveryTrackingInfo;
use App\Services\DeliveryTracking\DeliveryTrackingProvider;
use Illuminate\Support\Facades\Http;

class FaderDomesticTrackingProvider implements DeliveryTrackingProvider
{
    public function track(string $trackingId): ?DeliveryTrackingInfo
    {
        $sourceUrl = 'https://www.fdedomestic.com/track.php?track_number='.urlencode($trackingId);

        $response = Http::timeout(15)->accept('text/html')->get($sourceUrl);

        if (! $response->ok()) {
            return null;
        }

        $html = $response->body();

        if ($html === '') {
            return null;
        }

        $parsed = $this->parseHtml($html, $trackingId);

        if ($parsed === null) {
            return null;
        }

        return new DeliveryTrackingInfo(
            trackingId: $parsed['tracking_id'],
            status: $parsed['status'],
            location: $parsed['location'],
            lastUpdate: $parsed['last_update'],
            sourceUrl: $sourceUrl,
        );
    }

    /**
     * @return array{tracking_id:string,status:string,location:?string,last_update:?string}|null
     */
    private function parseHtml(string $html, string $fallbackTrackingId): ?array
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument;
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $trackingText = trim((string) $xpath->evaluate("string(//div[contains(@class,'card-body')]//h4[1])"));
        $status = trim((string) $xpath->evaluate("string(//div[contains(@class,'card-body')]//p[contains(@class,'text-muted')][1])"));
        $location = trim((string) $xpath->evaluate("string(//div[contains(@class,'card-body')]//a[contains(@class,'text-primary')][1])"));
        $lastUpdateText = trim((string) $xpath->evaluate("string(//div[contains(@class,'card-body')]//p[contains(.,'Last Update')][1])"));

        $trackingId = $fallbackTrackingId;

        if (preg_match('/Tracking ID\s*:\s*([^\s<]+)/i', $trackingText, $matches) === 1) {
            $trackingId = trim($matches[1]);
        }

        if ($status === '') {
            return null;
        }

        $lastUpdate = null;

        if ($lastUpdateText !== '' && preg_match('/Last Update\s*:\s*(.+)$/i', $lastUpdateText, $matches) === 1) {
            $lastUpdate = trim($matches[1]);
        }

        return [
            'tracking_id' => $trackingId,
            'status' => $status,
            'location' => $location !== '' ? $location : null,
            'last_update' => $lastUpdate,
        ];
    }
}
