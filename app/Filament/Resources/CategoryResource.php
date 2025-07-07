<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    public static function getPluralLabel(): string
    {
        return __('Categories');
    }

    public static function getLabel(): string
    {
        return __('Category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->translateLabel()
                    ->required()
                    ->options([
                        'game' => __('Game'),
                        'literature' => __('Literature'),
                        'item' => __('Item')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->translateLabel()
                    ->searchable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin==true;
    }
}
