<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Reserveditem;
use App\Filament\Widgets\Filament;
use Filament\Forms\Components\TextColumn;

class ReserveditemsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reserveditem::where('user_id', auth()->id())->where('returned', false)
            )
            ->heading('Dina lånade items')
            ->columns([
                Tables\Columns\TextColumn::make('item.desc')
                    ->label('Beskrivning')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('item.image')
                    ->label('Bild'),
                Tables\Columns\TextColumn::make('reserved_date')
                    ->label('Reserveringsdatum')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Återlämnningsdatum')
                    ->sortable(),
                Tables\Columns\IconColumn::make('delivered')
                    ->label('Utlämnad')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('returned')
                    ->label('Återlämnad')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ]);
    }
}
