<?php

namespace App\Filament\Resources\Deals\Pages;

use App\Filament\Resources\DealResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ViewInvoice extends Page
{
    protected static string $resource = DealResource::class;

    protected string $view = 'filament.resources.deals.pages.view-invoice';

    public Invoice $invoice;

    public function mount(int $record, Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->invoice->load('deal.contact');

        abort_unless($this->invoice->deal_id === $record, 404);
    }

    public function getTitle(): string
    {
        return 'Invoice '.$this->invoice->invoice_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Deal')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(DealResource::getUrl('edit', ['record' => $this->invoice->deal_id])),

            Action::make('print')
                ->label('Print / PDF')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn (): string => route('invoices.show', $this->invoice).'?print=1')
                ->openUrlInNewTab(),

            Action::make('parcelLabel')
                ->label('Parcel Label')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(fn (): string => route('invoices.parcel-label', $this->invoice).'?print=1')
                ->openUrlInNewTab(),
        ];
    }
}
