<?php

namespace App\Filament\Pages;

use Filament\Pages\BasePage;
use App\Filament\Widgets\StatsOverviewWidget;
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
use Filament\Infolists\Components\Section;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\ImageEntry;

class Reserve extends BasePage implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.reserve';

    
    public static function table(Table $table): Table
    {
        return $table
            ->query( Item::where('can_be_loaned', true)->where('type', "game"))
            ->columns([
                Tables\Columns\TextColumn::make('desc')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('players')
                    ->label('Players')
                    ->sortable(),
                Tables\Columns\TextColumn::make('play_time')
                    ->label('Play Time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->sortable(),
                Tables\Columns\IconColumn::make('reserved')
                    ->label('Available')
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueIcon('heroicon-o-x-mark')
                    ->falseColor('success')
                    ->trueColor('danger')
                    ->default(false),
                    
            ])
            ->filters([
                SelectFilter::make('category_id')
                ->label('Category')
                ->multiple()
                ->options(Category::all()->where('type', 'game')->pluck('name', 'id')),
                Filter::make('reserved')
                ->label('Available')
                ->query(fn (Builder $query): Builder => $query->where('reserved', false))
                ->columnSpanFull()
                ->toggle()
            ],layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('More info')
                ->modalSubmitAction(false)   
                ->infolist([
                    Section::make('Game')
                    ->schema([
                        ImageEntry::make('image'),
                        TextEntry::make('desc'),
                        TextEntry::make('acquisition_date'),
                        TextEntry::make('category.name'),
                        TextEntry::make('players'),
                        TextEntry::make('play_time'),
                        TextEntry::make('age'),
                    ])
                    ->columns(),
                    ]),
                Tables\Actions\Action::make('reserve')
                ->label('Reserve')
                ->button()
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('username')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required(),
                ])
                ->action(function (array $data, Item $record): void {
                    Reserveditem::create([
                        'item_id' => $record->id,
                        'reserved_date' => Carbon::now(),
                        'username' => $data['username'],
                        'email' => $data['email']
                    ]);
                    Item::where('id', $record->id)->update(['reserved' => true]);
                })
                ->hidden(fn ($record) => $record->reserved)
            ])
            ->bulkActions([
            ]);
    }
}
