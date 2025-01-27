<?php

namespace App\Filament\Resources\ReserveditemResource\Pages;

use App\Filament\Resources\ReserveditemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ItemResource;
use App\Models\Item;

class CreateReserveditem extends CreateRecord
{
    protected static string $resource = ReserveditemResource::class;

    protected static ?string $title = 'Reserve an item';

   protected function handleRecordCreation(array $data): Model
   {
        $data['user_id'] = auth()->id();
       $data['username'] = auth()->user()->name;
       $data['reserved_date'] = Carbon::now();

       if ($data['delivered'])
        {
            $data['delivered_date'] = Carbon::now();
            $data['return_date'] = Carbon::now()->addMonths(1);
        }

        if ($data['returned'])
        {
            $data['returned_date'] = Carbon::now();
        }

       // Item::where('id', $data['user_id'])->update(['reserved' => 'true']);
        
       return static::getModel()::create($data);
   }

   protected function afterCreate(): void
    {
        $record = $this->record;
        Item::where('id', $record->item_id)->update(['reserved' => true]);
    }

   protected function getRedirectUrl(): string
   {
       return $this->getResource()::getUrl('index');
   }

   protected function getFooterWidgets(): array
    {
        return [
            ItemResource\Widgets\ItemsOverview::class,
        ];
    }
}
