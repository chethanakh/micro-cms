<?php

namespace App\Filament\Resources\Deals\Pages;

use App\Filament\Resources\DealResource;
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
                ->action(function (): void {
                    if ($this->record->invoice_number) {
                        Notification::make()
                            ->title('Invoice already generated.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'invoice_number' => 'INV-'.str_pad((string) $this->record->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('YmdHis'),
                        'invoice_generated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Invoice placeholder generated.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
