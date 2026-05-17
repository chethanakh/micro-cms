<?php

namespace App\Filament\Pages;

use App\Models\CompanyInformation as CompanyInformationRecord;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class CompanyInformation extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Company Information';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.company-information';

    public ?array $data = [];

    public CompanyInformationRecord $record;

    public function mount(): void
    {
        $this->record = CompanyInformationRecord::current();

        $this->form->fill($this->record->attributesToArray());
    }

    public function getTitle(): string
    {
        return 'Company Information';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('company-information')
                            ->visibility('public')
                            ->helperText('Upload the company logo used in invoice views.')
                            ->columnSpanFull(),
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('mobile_number')
                            ->label('Mobile Number')
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
                    ])
                    ->columns(2),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Company information updated.')
            ->success()
            ->send();
    }
}
