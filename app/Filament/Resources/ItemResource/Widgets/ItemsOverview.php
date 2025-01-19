<?php

namespace App\Filament\Resources\ItemResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Item;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Post;
use Filament\Tables\Actions\Action;

class ItemsOverview extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Item::where('can_be_loaned', true)
            )
            ->heading('Items för utlåning')
            ->columns([
                Tables\Columns\TextColumn::make('desc')
                    ->label('Beskrivning')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Bild'),
                Tables\Columns\IconColumn::make('reserveditem.reserved')
                    ->label('Tillgänglig')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->default(true),
                Tables\Columns\TextColumn::make('reserveditem.return_date')
                    ->label('Tillbaka')
                    ->searchable(),
            ]);
    }
}
