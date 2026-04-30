<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contacts\Pages;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Select::make('channel_id')
                                    ->label('Channel')
                                    ->relationship('channel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                TextInput::make('phone_number')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('mobile_number')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('whatsapp_number')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                            ]),
                    ]),

                Section::make('Billing Address')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('billing_address_line_1')
                                    ->label('Billing Address Line 1')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('billing_address_line_2')
                                    ->label('Billing Address Line 2')
                                    ->maxLength(255),
                                TextInput::make('billing_city')
                                    ->label('Billing City')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('billing_postal_code')
                                    ->label('Billing Postal Code')
                                    ->required()
                                    ->maxLength(50),
                            ]),
                    ]),

                Checkbox::make('delivery_same_as_billing')
                    ->label('Delivery address is same as billing address')
                    ->default(true)
                    ->live(),

                Section::make('Delivery Address')
                    ->hidden(fn (Get $get): bool => (bool) $get('delivery_same_as_billing'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('delivery_address_line_1')
                                    ->label('Delivery Address Line 1')
                                    ->required(fn (Get $get): bool => ! $get('delivery_same_as_billing'))
                                    ->maxLength(255),
                                TextInput::make('delivery_address_line_2')
                                    ->label('Delivery Address Line 2')
                                    ->maxLength(255),
                                TextInput::make('delivery_city')
                                    ->label('Delivery City')
                                    ->required(fn (Get $get): bool => ! $get('delivery_same_as_billing'))
                                    ->maxLength(255),
                                TextInput::make('delivery_postal_code')
                                    ->label('Delivery Postal Code')
                                    ->required(fn (Get $get): bool => ! $get('delivery_same_as_billing'))
                                    ->maxLength(50),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Phone'),
                TextColumn::make('mobile_number')
                    ->label('Mobile'),
                TextColumn::make('channel.name')
                    ->label('Channel')
                    ->searchable(),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
