<?php

namespace App\Enums;

enum DealStage: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case HandedOverToDelivery = 'handed_over_to_delivery';
    case Delivered = 'delivered';
    case Closed = 'closed';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Preparing->value => 'Preparing',
            self::HandedOverToDelivery->value => 'Hand Overed to the Delivery',
            self::Delivered->value => 'Delivered',
            self::Closed->value => 'Colsed',
        ];
    }

    public function invoiceStatus(): string
    {
        return match ($this) {
            self::Preparing, self::HandedOverToDelivery => 'sent',
            self::Delivered => 'accepted',
            self::Closed => 'paid',
            self::Pending => 'quote',
        };
    }

    public function label(): string
    {
        return self::options()[$this->value];
    }
}
