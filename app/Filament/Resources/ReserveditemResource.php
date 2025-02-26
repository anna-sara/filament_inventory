<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReserveditemResource\Pages;
use App\Filament\Resources\ReserveditemResource\RelationManagers;
use App\Models\Reserveditem;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Radio;
use Filament\Support\Enums\IconPosition;


class ReserveditemResource extends Resource
{
    protected static ?string $model = Reserveditem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static ?string $modelLabel = 'Reservations';

    protected static ?string $title = 'Reserve an item';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                   ->label('Name')
                   ->default(null),
                Forms\Components\TextInput::make('email')
                   ->label('Email')
                   ->default(null),
                Section::make('')
                ->schema([
                    Forms\Components\Toggle::make('delivered'),
                    Forms\Components\Toggle::make('returned')
                ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            //->query(Reserveditem::withTrashed())
            ->columns([
                Tables\Columns\TextColumn::make('item.desc')
                    ->label('Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reserved_date')
                    ->label('Reservation date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivered_date')
                    ->label('Delivery date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Return date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('returned_date')
                    ->label('Returned')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->button()
                ->icon('heroicon-m-pencil-square')
                ->iconPosition(IconPosition::After),
            ])
            ->bulkActions([
            //    Tables\Actions\BulkActionGroup::make([
            //        Tables\Actions\DeleteBulkAction::make(),
            //    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReserveditems::route('/'),
            'create' => Pages\CreateReserveditem::route('/create'),
            'edit' => Pages\EditReserveditem::route('/{record}/edit'),
        ];
    }



public static function canCreate(): bool
    {
        return false;
    }


public static function canViewAny(): bool
    {
        return auth()->user()->is_admin==true;
    }
}
