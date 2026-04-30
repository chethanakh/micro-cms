<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\ContactResource;
use App\Filament\Resources\DealResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createDeal')
                ->label('Create Deal')
                ->url(fn (): string => DealResource::getUrl('create', ['contact_id' => $this->record->id])),
            DeleteAction::make(),
        ];
    }
}
