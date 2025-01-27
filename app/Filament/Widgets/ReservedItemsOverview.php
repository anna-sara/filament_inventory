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
            ->heading('Your reserved items')
            ->columns([
                Tables\Columns\TextColumn::make('item.desc')
                    ->label('Description')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('item.image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('reserved_date')
                    ->label('Reserved date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Return date')
                    ->sortable(),
                Tables\Columns\IconColumn::make('delivered')
                    ->label('Delivered')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('returned')
                    ->label('Returned')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ]);
    }
}
