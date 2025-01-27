<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Models\User;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Items';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                ->label('Image')
                ->minSize(512)
                ->maxSize(5500)
                ->columnSpan('full')
                ->image(),
                Forms\Components\TextInput::make('desc')
                    ->label('Description')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('acquisition_date')
                    ->label('Acquisition date'),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000)
                    ->default(0),
                Forms\Components\Select::make('type_id')
                    ->label('Type')
                    ->options(Type::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('cost')
                    ->label('Price')
                    ->default(null),
                Forms\Components\Toggle::make('can_be_loaned')
                    ->label('Can be loaned'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('desc')
                    ->label('Description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('acquisition_date')
                    ->label('Acquisition date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Price')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),
                Tables\Columns\IconColumn::make('can_be_loaned')
                    ->label('Can be loaned')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                //Tables\Columns\TextColumn::make('reserveditems.reserved')
                //    ->label('Ska returneras'),
                //Tables\Columns\TextColumn::make('reserveditems.name')
                //    ->label('Utlånad till')
                    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin==true;
    }

}
