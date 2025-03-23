<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use App\Models\Reserveditem;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCreatedUser;
use App\Mail\ReservationCreated;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('type')
                    ->options([
                        'game' => 'Game',
                        'item' => 'Item',
                    ])
                    ->default('game')
                    ->live(),
                FileUpload::make('image')
                    ->label('Image')
                    ->minSize(25)
                    ->maxSize(5500)
                    ->columnSpan('full')
                    ->disk('public')
                    ->image(),
                TextInput::make('desc')
                    ->label('Description')
                    ->maxLength(255)
                    ->default(null),
                DatePicker::make('acquisition_date')
                    ->label('Acquisition date'),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000)
                    ->default(0)
                    ->hidden(fn ($get): string => $get('type') == 'game'),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id')),
                TextInput::make('cost')
                    ->label('Price')
                    ->default(null),
                TextInput::make('age')
                    ->label('Age')
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item'),
                TextInput::make('players')
                    ->label('Players')
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item'),
                TextInput::make('play_time')
                    ->label('Play time')
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item'),
                Toggle::make('can_be_loaned')
                    ->label('Can be loaned')
                    ->hidden(fn ($get): string => $get('type') == 'game'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('desc')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label('Image'),
                IconColumn::make('can_be_loaned')
                    ->label('Can be loaned')
                    ->sortable()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('reserved')
                    ->label('Available')
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueIcon('heroicon-o-x-mark')
                    ->falseColor('success')
                    ->trueColor('danger')
                    ->default(false),
            ])
            ->filters([
                SelectFilter::make('type')
                ->multiple()
                ->options([
                    'game' => 'Game',
                    'item' => 'Item',
                ]),
                SelectFilter::make('category_id')
                ->label('Item Category')
                ->multiple()
                ->options(Category::all()->where('type', 'item')->pluck('name', 'id')),
                SelectFilter::make('category_id')
                ->label('Category')
                ->multiple()
                ->options(
                    Category::all()->pluck('name', 'id'),
                ),
                Filter::make('reserved')
                ->label('Available')
                ->query(fn (Builder $query): Builder => $query->where('reserved', false))
                ->columnSpanFull()
                ->toggle(),
                Filter::make('can_be_loaned')
                ->label('Can be loaned')
                ->query(fn (Builder $query): Builder => $query->where('can_be_loaned', true))
                ->columnSpanFull()
                ->toggle()
            ],layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('More info')
                ->modalSubmitAction(false)   
                ->infolist([
                    Section::make('Game')
                    ->schema([
                        ImageEntry::make('image') 
                        ->width(300)
                        ->height('auto'),
                        TextEntry::make('desc'),
                        TextEntry::make('acquisition_date'),
                        TextEntry::make('category.name'),
                        TextEntry::make('players'),
                        TextEntry::make('play_time'),
                        TextEntry::make('age'),
                        TextEntry::make('cost'),
                    ])
                    ->columns()
                    ->hidden(fn ($record) => $record->type === "item"),
                    Section::make('Item')
                    ->schema([
                        ImageEntry::make('image'),
                        TextEntry::make('desc'),
                        TextEntry::make('acquisition_date'),
                        TextEntry::make('category.name'),
                        TextEntry::make('quantity'),
                        TextEntry::make('cost'),
                    ])
                    ->columns()
                    ->hidden(fn ($record) => $record->type === "game"),
                    ]),
               
                Tables\Actions\EditAction::make()
                ->button()
                ->icon('heroicon-m-pencil-square')
                ->iconPosition(IconPosition::After),
                Tables\Actions\Action::make('reserve')
                ->label('Reserve')
                ->button()
                ->color('success')
                ->form([
                    TextInput::make('username')
                        ->label('Name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->required(),
                ])
                ->action(function (array $data, Item $record): void {
                    $reservation = Reserveditem::create([
                        'item_id' => $record->id,
                        'reserved_date' => Carbon::now(),
                        'username' => $data['username'],
                        'email' => $data['email']
                    ]);
                    Item::where('id', $record->id)->update(['reserved' => true]);
                    Mail::to($data['email'])
                    ->send(new ReservationCreatedUser($reservation));
                    Mail::to(env('MAIL_FROM_ADDRESS'))
                    ->send(new ReservationCreated($reservation));
                })
                ->hidden(fn ($record) => $record->reserved)
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
