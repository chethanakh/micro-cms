<?php

namespace App\Filament\Resources;

use App\Enums\DealStage;
use App\Enums\DeliveryServiceProvider;
use App\Filament\Resources\Deals\Pages;
use App\Filament\Resources\Deals\RelationManagers\InvoicesRelationManager;
use App\Models\CompanyInformation;
use App\Models\Deal;
use App\Models\Stock;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([Section::make('Deal')
                ->schema([
                    Select::make('contact_id')
                        ->label('Contact')
                        ->relationship('contact', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn ($record): string => trim("{$record->first_name} {$record->last_name}"))
                        ->searchable(['first_name', 'last_name', 'email', 'phone_number', 'mobile_number'])
                        ->preload()
                        ->default(fn (): ?int => request()->integer('contact_id') ?: null)
                        ->required(),
                ]),
                Section::make('Action List')
                    ->schema([
                        Select::make('stage')
                            ->label('Deal Stage')
                            ->live()
                            ->options(DealStage::options())
                            ->default('pending')
                            ->afterStateUpdated(function (?string $state, ?Deal $record): void {
                                if (! $record || $state === null || $record->stage === $state) {
                                    return;
                                }

                                $record->update([
                                    'stage' => $state,
                                ]);
                            })
                            ->required(),
                        Placeholder::make('invoice_status')
                            ->label('Invoice')
                            ->content(fn (?Deal $record): string => ($latest = $record?->latestInvoice)
                                ? "Invoice generated: {$latest->invoice_number}"
                                : 'No invoice generated yet.'),
                    ]),

                Section::make('Line Items')
                    ->schema([
                        Repeater::make('lineItems')
                            ->relationship()
                            ->defaultItems(1)
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                                        if (! $state) {
                                            $set('stock_id', null);
                                            $set('unit_price', null);

                                            return;
                                        }

                                        $stocks = Stock::query()
                                            ->where('product_id', $state)
                                            ->whereRaw('(quantity - sold_quantity) > 0')
                                            ->orderBy('id')
                                            ->get(['id', 'retail_price']);

                                        if ($stocks->count() !== 1) {
                                            $set('stock_id', null);

                                            return;
                                        }

                                        $stock = $stocks->first();

                                        $set('stock_id', $stock?->id);
                                        $set('unit_price', $stock?->retail_price);
                                    }),
                                Select::make('stock_id')
                                    ->label('Stock')
                                    ->options(function (Get $get): array {
                                        $productId = $get('product_id');

                                        if (! $productId) {
                                            return [];
                                        }

                                        return Stock::query()
                                            ->where('product_id', $productId)
                                            ->whereRaw('(quantity - sold_quantity) > 0')
                                            ->orderBy('id')
                                            ->get(['id', 'quantity', 'sold_quantity', 'retail_price'])
                                            ->mapWithKeys(function (Stock $stock): array {
                                                $available = max(0, $stock->quantity - $stock->sold_quantity);

                                                return [
                                                    $stock->id => "Stock #{$stock->id} (Available: {$available})",
                                                ];
                                            })
                                            ->all();
                                    })
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $stock = Stock::query()->find($state);

                                        if (! $stock) {
                                            return;
                                        }

                                        $set('unit_price', $stock->retail_price);
                                    }),
                                TextInput::make('quantity')
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->minValue(1),
                                TextInput::make('unit_price')
                                    ->label('Price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                            ])
                            ->columns(4)
                            ->minItems(1)
                            ->required(),
                    ])
                    ->columnSpan(2),

                Section::make('Delivery')
                    ->schema([
                        Checkbox::make('delivery_charges_available')
                            ->label('Delivery charges available')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?bool $state): void {
                                if ($state) {
                                    $defaultCharges = CompanyInformation::current()->default_delivery_charges ?? 0;
                                    $set('delivery_charges', $defaultCharges);
                                    if (blank($get('delivery_service_provider'))) {
                                        $set('delivery_service_provider', DeliveryServiceProvider::FaderDomestic->value);
                                    }
                                } else {
                                    $set('delivery_charges', null);
                                    $set('delivery_service_provider', null);
                                    $set('tracking_id', null);
                                }
                            }),
                        TextInput::make('delivery_charges')
                            ->label('Delivery charges')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('Rs.')
                            ->visible(fn (Get $get) => (bool) $get('delivery_charges_available')),
                        Select::make('delivery_service_provider')
                            ->label('Delivery Service Provider')
                            ->options(DeliveryServiceProvider::options())
                            ->visible(fn (Get $get) => (bool) $get('delivery_charges_available')),
                        TextInput::make('tracking_id')
                            ->label('Tracking ID')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => (bool) $get('delivery_charges_available')),
                        Placeholder::make('tracking_public_url')
                            ->label('Public Tracking URL')
                            ->content(function (?Deal $record): string {
                                if (! $record?->tracking_slug) {
                                    return 'Save this deal to generate a public tracking URL.';
                                }

                                return route('tracking.public', ['slug' => $record->tracking_slug]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('contact.first_name')
                    ->label('Contact First Name')
                    ->searchable(),
                TextColumn::make('contact.last_name')
                    ->label('Contact Last Name')
                    ->searchable(),
                TextColumn::make('line_items_count')
                    ->label('Items')
                    ->counts('lineItems')
                    ->sortable(),
                TextColumn::make('stage')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => DealStage::tryFrom($state ?? '')?->label() ?? match ($state) {
                        'new' => 'Pending',
                        default => (string) $state,
                    })
                    ->sortable(),
                TextColumn::make('latestInvoice.invoice_number')
                    ->label('Invoice')
                    ->placeholder('Not generated')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
        return [
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeals::route('/'),
            'create' => Pages\CreateDeal::route('/create'),
            'edit' => Pages\EditDeal::route('/{record}/edit'),
            'view-invoice' => Pages\ViewInvoice::route('/{record}/invoices/{invoice}'),
        ];
    }
}
