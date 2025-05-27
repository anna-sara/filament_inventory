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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationDeletedUser;


class ReserveditemResource extends Resource
{
    protected static ?string $model = Reserveditem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    public static function getNavigationLabel(): string
    {
        return __('Reservations');
    }

    public static function getPluralLabel(): string
    {
        return __('Reservations');
    }

    public static function getLabel(): string
    {
        return __('Reservation');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                   ->label('Name')
                   ->translateLabel()
                   ->default(null),
                TextInput::make('email')
                   ->label('Email')
                   ->default(null),
                Section::make('')
                ->schema([
                    Toggle::make('delivered')
                    ->translateLabel(),
                    Toggle::make('returned')
                    ->translateLabel()
                ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            //->query(Reserveditem::withTrashed())
            ->columns([
                Stack::make([
                    TextColumn::make('item.desc')
                        ->label('Name')
                        ->translateLabel()
                        ->sortable()
                        ->weight(FontWeight::Bold)
                        ->size(TextColumn\TextColumnSize::Large),
                    TextColumn::make('username')
                        ->label('User')
                        ->translateLabel()
                        ->sortable()
                        ->icon('heroicon-m-user'),
                    TextColumn::make('email')
                        ->label('Email')
                        ->sortable()
                        ->icon('heroicon-m-envelope'),
                    TextColumn::make('reserved_date')
                        ->label('Reservation date')
                        ->translateLabel()
                        ->sortable()
                        ->dateTime('Y-m-d')
                        ->icon('heroicon-m-hand-raised'),
                    TextColumn::make('delivered_date')
                        ->label('Delivery date')
                        ->translateLabel()
                        ->sortable()
                        ->dateTime('Y-m-d')
                        ->icon('heroicon-m-arrow-up-tray')
                        ,
                    TextColumn::make('return_date')
                        ->label('Return date')
                        ->translateLabel()
                        ->sortable()
                        ->dateTime('Y-m-d')
                        ->icon('heroicon-m-arrow-down-tray')   
                ])->extraAttributes(['class' => 'space-y-2']) 
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->button()
                ->icon('heroicon-m-pencil-square')
                ->iconPosition(IconPosition::After),
                Tables\Actions\DeleteAction::make()
                ->action(function (array $data, Reserveditem $record): void {
                    $record->delete();
                    Item::where('id', $record->item_id)->update(['reserved' => false]);
                    Mail::to($record['email'])
                    ->send(new ReservationDeletedUser($record));
                })
                
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
