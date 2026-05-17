<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Vendors\Pages\CreateVendor;
use App\Filament\Resources\Vendors\Pages\EditVendor;
use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Models\Vendor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Purcheses';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('mobile_number')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('address_line_1')
                            ->label('Address Line 1')
                            ->maxLength(255),
                        TextInput::make('address_line_2')
                            ->label('Address Line 2')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(255),
                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->maxLength(50),
                        Textarea::make('special_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Phone'),
                TextColumn::make('mobile_number')
                    ->label('Mobile'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }
}
