<?php

namespace App\Filament\Resources\Deals\Pages;

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

                    $invoice = Invoice::create([
                        'deal_id' => $deal->id,
                        'invoice_number' => 'INV-'.str_pad((string) $deal->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('YmdHis'),
                        'status' => 'quote',
                        'line_items' => $lineItems,
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

            DeleteAction::make(),
        ];
    }
}
