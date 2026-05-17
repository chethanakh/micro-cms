<?php

namespace App\Enums;

enum DeliveryServiceProvider: string
{
    case FaderDomestic = 'fader_domestic';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::FaderDomestic->value => 'FaderDomestic',
        ];
    }

    public function label(): string
    {
        return self::options()[$this->value];
    }

    public function logoPath(): string
    {
        return match ($this) {
            self::FaderDomestic => asset('images/delivery/fader-domestic.png'),
        };
    }

    public function trackingUrl(string $trackingId): string
    {
        return match ($this) {
            self::FaderDomestic => 'https://www.fdedomestic.com/track.php?track_number='.urlencode($trackingId),
        };
    }
}
