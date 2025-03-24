<?php

namespace App\Filament\Resources\ReserveditemResource\Pages;

use App\Filament\Resources\ReserveditemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReserveditems extends ListRecords
{
    protected static string $resource = ReserveditemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create new')
            ->translateLabel(),
        ];
    }
}
