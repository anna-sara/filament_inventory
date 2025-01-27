<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReserveditemResource\Pages;
use App\Filament\Resources\ReserveditemResource\RelationManagers;
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


class ReserveditemResource extends Resource
{
    protected static ?string $model = Reserveditem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static ?string $modelLabel = 'Reserverade items';

    protected static ?string $title = 'Reserverade item';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                ->label('Choose item to reserve')
                   ->relationship(
                       name: 'item', 
                       titleAttribute: 'desc',
                        modifyQueryUsing: fn ($query) =>  $query->where('can_be_loaned', true)->where('reserved', false)
                   )
                   ->required()
                   ->disabledOn('edit') 
                   ->hiddenOn('edit'),
                //Forms\Components\TextInput::make('user_id')
                //    ->label('Användare')
                //    ->default(auth()->id())
                //    ->disabledOn(['edit', 'create']) 
                //    ->hiddenOn('edit'),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReserveditems::route('/'),
            'create' => Pages\CreateReserveditem::route('/create'),
            'edit' => Pages\EditReserveditem::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
{
    return [
        ItemResource\Widgets\ItemsOverview::class,
    ];
}

public static function canViewAny(): bool
    {
        return auth()->user()->is_admin==true;
    }
}
