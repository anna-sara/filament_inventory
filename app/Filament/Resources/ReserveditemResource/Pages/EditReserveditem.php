<?php

namespace App\Filament\Resources\ReserveditemResource\Pages;

use App\Filament\Resources\ReserveditemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Reserveditem;
use Illuminate\Support\Facades\Mail;
use App\Mail\Delivered;

class EditReserveditem extends EditRecord
{
    protected static string $resource = ReserveditemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

     protected function mutateFormDataBeforeSave(array $data): array
    {
       
       if ($data['delivered'])
        {
            $data['delivered_date'] = Carbon::now();
            $data['return_date'] = Carbon::now()->addMonths(1);
        }

        if ($data['returned'])
        {
            $data['returned_date'] = Carbon::now();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        if ($record->returned) {
            Item::where('id', $record->item_id)->update(['reserved' => false]);
            Reserveditem::where('id', $record->id)->delete();
        }

        if ($record->delivered && !$record->returned)
        {
            Mail::to($record->email)
            ->send(new Delivered($record));
        }

       
        
    }

    protected function getRedirectUrl(): string
   {
       return $this->getResource()::getUrl('index');
   }

   public function getHeading(): string
    {
        return Item::where('id', $this->getRecord()->item_id)->pluck('desc')->first();
    }
}
