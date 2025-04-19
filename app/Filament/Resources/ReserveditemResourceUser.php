<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReserveditemResourceUser\Pages;
use App\Filament\Resources\ReserveditemResourceUser\RelationManagers;
use App\Models\Reserveditem;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class ReserveditemResourceUser extends Resource
{
    protected static ?string $model = Reserveditem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static ?string $modelLabel = 'Reserve items';

    protected static ?string $slug = 'user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                ->label('Choose an item to reserve')
                   ->relationship(
                       name: 'item', 
                       titleAttribute: 'desc',
                       modifyQueryUsing: fn ($query) =>  $query->where('can_be_loaned', true)->where('reserved', false)
                   )
                   ->required(),
                //Forms\Components\TextInput::make('user_id')
                //    ->label('Användare')
                //    ->default(auth()->id())
                //    ->disabledOn('create') 
                //    //->hiddenOn('create')
                //    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('items.desc')
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('username')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('reserved_date')
                    ->label('Reserved date')
                    ->sortable(),
                TextColumn::make('delivered_date')
                    ->label('Delivery date')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return date')
                    ->sortable(),
                TextColumn::make('returned_date')
                    ->label('Returned')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\CreateReserveditemUser::route('/'),
        ];
    }



    public static function canViewAny(): bool
    {
        return false;
        //return auth()->user()->is_admin==false;
    }
}
