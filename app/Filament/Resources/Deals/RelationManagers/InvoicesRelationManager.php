<?php

namespace App\Filament\Resources\Deals\RelationManagers;

use App\Filament\Resources\DealResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Invoice::STATUS_OPTIONS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'sent' => 'info',
                        'paid' => 'success',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('line_items')
                    ->label('Items')
                    ->formatStateUsing(fn (mixed $state): string => count((array) $state).' item(s)'),
                TextColumn::make('created_at')
                    ->label('Generated')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Invoice $record): string => DealResource::getUrl('view-invoice', [
                        'record' => $record->deal_id,
                        'invoice' => $record->id,
                    ])),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
