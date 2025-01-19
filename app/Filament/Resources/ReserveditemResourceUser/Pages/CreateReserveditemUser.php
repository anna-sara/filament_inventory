<?php

namespace App\Filament\Resources\ReserveditemResourceUser\Pages;

use App\Filament\Resources\ReserveditemResourceUser;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ItemResource;
use App\Models\Item;

class CreateReserveditemUser extends CreateRecord
{
    protected static string $resource = ReserveditemResourceUser::class;

    protected static ?string $title = 'Reserverade item';

   protected function handleRecordCreation(array $data): Model
   {
    $data['user_id'] = auth()->id();
       $data['username'] = auth()->user()->name;
       $data['reserved_date'] = Carbon::now();
       $data['delivered'] = false;
       $data['returned'] = false;
        $data['delivered_date'] = null;
        $data['return_date'] = null;;
        $data['returned_date'] = null;
    

       return static::getModel()::create($data);
   }

   protected function getRedirectUrl(): string
   {
       return $this->getResource()::getUrl('index');
   }

   protected function afterCreate(): void
   {
       $record = $this->record;
       Item::where('id', $record->item_id)->update(['reserved' => true]);
   }

   protected function getFooterWidgets(): array
    {
        return [
            ItemResource\Widgets\ItemsOverview::class,
        ];
    }
}
