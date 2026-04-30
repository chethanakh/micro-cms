<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cost_price')
                    ->label('Cost Price')
                    ->required()
                    ->numeric(),
                TextInput::make('retail_price')
                    ->label('Retail Price')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->label('Bought Quantity')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->minValue(1),
                DatePicker::make('purchase_date')
                    ->label('Purchase Date')
                    ->default(now())
                    ->required(),
                Select::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cost_price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('retail_price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Bought Qty')
                    ->sortable(),
                TextColumn::make('sold_quantity')
                    ->label('Sold Qty')
                    ->sortable(),
                TextColumn::make('available_quantity')
                    ->label('Available Qty')
                    ->state(fn ($record): int => max(0, (int) $record->quantity - (int) $record->sold_quantity))
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw("(quantity - sold_quantity) {$direction}")),
                TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Refilled At')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Refill Stock'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
