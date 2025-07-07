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
use Filament\Forms\Components\Checkbox;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getNavigationLabel(): string
    {
        return __('Inventory');
    }

    public static function getPluralLabel(): string
    {
        return __('Inventory');
    }

    public static function getLabel(): string
    {
        return __('Inventory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('type')
                    ->translateLabel()
                    ->options([
                        'game' => __('Game'),
                        'item' => __('Item'),
                        'literature' => __('Literature')
                    ])
                    ->default('game')
                    ->live(),
                FileUpload::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->translateLabel()
                    ->minSize(25)
                    ->maxSize(5500)
                    ->columnSpan('full')
                    ->image(),
                TextInput::make('desc')
                    ->label('Description')
                    ->translateLabel()
                    ->maxLength(255)
                    ->default(null),
                DatePicker::make('acquisition_date')
                    ->label('Acquisition date')
                    ->translateLabel(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->translateLabel()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000)
                    ->default(0)
                    ->hidden(fn ($get): string => $get('type') == 'game' || $get('type') == 'literature' ),
                Select::make('category_id')
                    ->label('Category')
                    ->translateLabel()
                    ->options(Category::all()->pluck('name', 'id')),
                TextInput::make('cost')
                    ->label('Price')
                    ->translateLabel()
                    ->default(null),
                TextInput::make('age')
                    ->label('Age')
                    ->translateLabel()
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item' || $get('type') == 'literature' ),
                TextInput::make('players')
                    ->label('Players')
                    ->translateLabel()
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item' || $get('type') == 'literature' ),
                TextInput::make('play_time')
                    ->label('Play time')
                    ->translateLabel()
                    ->maxLength(255)
                    ->default(null)
                    ->hidden(fn ($get): string => $get('type') == 'item'|| $get('type') == 'literature' ),
                Toggle::make('can_be_loaned')
                    ->label('Loanable')
                    ->translateLabel()
                    ->hidden(fn ($get): string => $get('type') == 'game' || $get('type') == 'literature' ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('desc')
                    ->label('Description')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                //TextColumn::make('type')
                //    ->label('Type')
                //    ->translateLabel()
                //    ->searchable()
                //    ->sortable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->translateLabel()
                    ->extraImgAttributes([
                        'class' => 'rounded-md',
                        'loading' => 'lazy'
                    ]),
                IconColumn::make('can_be_loaned')
                    ->label('Loanable')
                    ->translateLabel()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('reserved')
                    ->label('Available')
                    ->sortable()
                    ->translateLabel()
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueIcon('heroicon-o-x-mark')
                    ->falseColor('success')
                    ->trueColor('danger')
                    ->default(false),
            ])
            ->filters([
                Filter::make('filters')
                ->form([
                    Select::make('type')
                        ->label('Type')
                        ->live()
                        ->translateLabel()
                        ->options([
                            'game' => __('Game'),
                            'literature' => __('Literature'),
                            'item' => __('Item')
                        ]),
                    Select::make('category_id')
                        ->multiple()
                        ->translateLabel()
                        ->label('Category literature')
                        ->options(Category::all()->whereIn('type', 'literature')->pluck('name', 'id'))
                        ->hidden(fn ($get): string   => $get('type') == 'game' || $get('type') == 'item'|| $get('type') == ''),
                    Select::make('category_id')
                        ->multiple()
                        ->translateLabel()
                        ->label('Category games')
                        ->options(Category::all()->whereIn('type', 'game')->pluck('name', 'id'))
                        ->hidden(fn ($get): string  => $get('type') == 'literature' ||  $get('type') == 'item'|| $get('type') == ''),
                    Select::make('category_id')
                        ->multiple()
                        ->translateLabel()
                        ->label('Category items')
                        ->options(Category::all()->whereIn('type', 'item')->pluck('name', 'id'))
                        ->hidden(fn ($get): string  => $get('type') == 'literature' || $get('type') == 'game'|| $get('type') == ''),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['type'],
                            fn (Builder $query, $type): Builder => $query->where('type', $type),
                        )
                        ->when(
                            $data['category_id'],
                            fn (Builder $query, $category_id): Builder => $query->whereIn('category_id', $category_id),
                        );
                })
                ->columns([
                    'deafult' => 1,
                    'md' => 2,
                ])
                ->columnSpan(2),
                Filter::make('reserved')
                ->label('Available')
                ->translateLabel()
                ->query(fn (Builder $query): Builder => $query->where('reserved', false))
                ->columnSpanFull()
                ->toggle(),
                Filter::make('can_be_loaned')
                ->label('Loanable')
                ->translateLabel()
                ->query(fn (Builder $query): Builder => $query->where('can_be_loaned', true))
                ->columnSpanFull()
                ->toggle()
            ],layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('More info')
                ->translateLabel()
                ->modalSubmitAction(false)   
                ->infolist([
                    Section::make('')
                    ->schema([
                        ImageEntry::make('image') 
                        ->translateLabel()
                        ->width(300)
                        ->height('auto'),
                        //->disk('local')
                        //->visibility('private'),
                        TextEntry::make('desc')
                        ->label('Description')
                        ->translateLabel(),
                        TextEntry::make('acquisition_date')
                        ->translateLabel(),
                        TextEntry::make('category.name')
                        ->translateLabel(),
                        TextEntry::make('players')
                        ->translateLabel(),
                        TextEntry::make('play_time')
                        ->translateLabel(),
                        TextEntry::make('age')
                        ->translateLabel(),
                        TextEntry::make('cost')
                        ->translateLabel(),
                    ])
                    ->columns()
                    ->hidden(fn ($record) => $record->type === "item" || $record->type === 'literature'),
                    Section::make('')
                    ->translateLabel()
                    ->schema([
                        ImageEntry::make('image')
                        ->translateLabel(),
                        TextEntry::make('desc')
                        ->label('Description')
                        ->translateLabel(),
                        TextEntry::make('acquisition_date')
                        ->translateLabel(),
                        TextEntry::make('category.name')
                        ->translateLabel(),
                        TextEntry::make('quantity')
                        ->translateLabel(),
                        TextEntry::make('cost')
                        ->translateLabel(),
                    ])
                    ->columns()
                    ->hidden(fn ($record) => $record->type === "game" || $record->type === 'literature'),
                    Section::make('')
                    ->translateLabel()
                    ->schema([
                        ImageEntry::make('image')
                        ->translateLabel(),
                        TextEntry::make('desc')
                        ->label('Description')
                        ->translateLabel(),
                        TextEntry::make('acquisition_date')
                        ->translateLabel(),
                        TextEntry::make('category.name')
                        ->translateLabel(),
                        TextEntry::make('cost')
                        ->translateLabel(),
                    ])
                    ->columns()
                    ->hidden(fn ($record) => $record->type === "game" || $record->type === 'item'),
                ]),
               
                Tables\Actions\EditAction::make()
                ->button()
                ->icon('heroicon-m-pencil-square')
                ->iconPosition(IconPosition::After),
                Tables\Actions\Action::make('reserve')
                ->label('Reserve')
                ->translateLabel()
                ->button()
                ->color('primary')
                ->form([
                    TextInput::make('username')
                        ->label('Name')
                        ->translateLabel()
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->translateLabel()
                        ->required(),
                    TextInput::make('phone')
                        ->label('Phone')
                        ->translateLabel()
                        ->tel() 
                        ->required(),
                    Checkbox::make('gdpr')
                        ->label('Genom att checka i denna rutan godkänner du att vi lagrar din mailadress och telefonnummer.')
                        ->accepted(),
                ])
                ->action(function (array $data, Item $record): void {
                    $reservation = Reserveditem::create([
                        'item_id' => $record->id,
                        'reserved_date' => Carbon::now(),
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'phone' => $data['phone']
                    ]);
                    Item::where('id', $record->id)->update(['reserved' => true]);
                    Mail::to($data['email'])
                    ->send(new ReservationCreatedUser($reservation));
                    Mail::to(env('MAIL_TO_ADDRESS'))
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
