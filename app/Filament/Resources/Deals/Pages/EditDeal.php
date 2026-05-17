<?php

namespace App\Filament\Resources\Deals\Pages;

use App\Enums\DealStage;
use App\Filament\Resources\DealResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDeal extends EditRecord
{
    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateInvoice')
                ->label('Generate Invoice')
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->action(function (): void {
                    $deal = $this->record;
                    $deal->load('lineItems.product');

                    $lineItems = $deal->lineItems->map(fn ($item): array => [
                        'product_name' => $item->product?->name ?? 'Unknown Product',
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                    ])->values()->all();

                    $deliveryCharges = $deal->delivery_charges_available ? ($deal->delivery_charges ?? 0) : 0;

                    $invoice = Invoice::create([
                        'deal_id' => $deal->id,
                        'invoice_number' => 'INV-'.str_pad((string) $deal->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('YmdHis'),
                        'status' => 'quote',
                        'line_items' => $lineItems,
                        'delivery_charges' => $deliveryCharges,
                    ]);

                    Notification::make()
                        ->title("Invoice {$invoice->invoice_number} generated.")
                        ->success()
                        ->send();
                }),

            Action::make('viewInvoice')
                ->label('View Invoice')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (): string => ($latest = $this->record->invoices()->latest()->first())
                    ? DealResource::getUrl('view-invoice', ['record' => $this->record->id, 'invoice' => $latest->id])
                    : '#')
                ->visible(fn (): bool => $this->record->invoices()->exists()),

            Action::make('sendWhatsApp')
                ->label('Send WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->visible(fn (): bool => filled($this->record->tracking_slug)
                    && filled($this->record->contact?->whatsapp_number)
                    && ! in_array($this->record->stage, [DealStage::Pending->value, DealStage::Closed->value], true))
                ->url(function (): string {
                    $deal = $this->record;
                    $customerName = trim(($deal->contact?->first_name ?? '').' '.($deal->contact?->last_name ?? ''));
                    $trackingUrl = route('tracking.public', ['slug' => $deal->tracking_slug]);
                    $message = urlencode("Hi {$customerName},\n\nHello {$customerName}, 👋\n\nYour order has been placed successfully! 📦\nYou can track your order status using the link below:\n\n{$trackingUrl}\n\nThank you for shopping with us. If you need any assistance, feel free to contact us.");

                    return "https://wa.me/{$deal->contact->whatsapp_number}?text={$message}";
                })
                ->openUrlInNewTab(),

            Action::make('copyTrackingUrl')
                ->label('Copy Tracking URL')
                ->icon('heroicon-o-clipboard-document')
                ->color('info')
                ->visible(fn (): bool => filled($this->record->tracking_slug)
                    && ! in_array($this->record->stage, [DealStage::Pending->value, DealStage::Closed->value], true))
                ->action(function (): void {
                    // Handled client-side via extraAttributes JS
                })
                ->extraAttributes(function (): array {
                    $url = filled($this->record->tracking_slug)
                        ? route('tracking.public', ['slug' => $this->record->tracking_slug])
                        : '';

                    return [
                        'onclick' => "event.preventDefault(); navigator.clipboard.writeText('{$url}').then(() => { this.textContent = '✓ Copied!'; setTimeout(() => { this.innerHTML = '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184\'/></svg> Copy Tracking URL'; }, 2000); })",
                    ];
                }),

            DeleteAction::make(),
        ];
    }
}
