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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Support\Colors\Color;

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
                TextColumn::make('desc')
                    ->label('Beskrivning')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Bild')
                    ->disk('local')
                    ->visibility('private'),
                TextColumn::make('players')
                    ->label('Antal spelare')
                    ->sortable(),
                TextColumn::make('play_time')
                    ->label('Speltid')
                    ->sortable(),
                TextColumn::make('age')
                    ->label('Ålder')
                    ->sortable(),
                IconColumn::make('reserved')
                    ->label('Tillgängligt')
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueIcon('heroicon-o-x-mark')
                    ->falseColor('success')
                    ->trueColor('danger')
                    ->default(false),
                    
            ])
            ->filters([
                SelectFilter::make('category_id')
                ->multiple()
                ->preload()
                ->label('Kategori')
                ->options(Category::all()->where('type', 'game')->pluck('name', 'id')),
                Filter::make('reserved')
                ->label('Tillgänglig')
                ->query(fn (Builder $query): Builder => $query->where('reserved', false))
                ->columnSpanFull()
                ->toggle()
            ],layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('Mer info')
                ->modalSubmitAction(false)   
                ->infolist([
                    Section::make('Spel')
                    ->schema([
                        ImageEntry::make('image')
                        ->label('Bild')
                        ->width(300)
                        ->height('auto')
                        ->disk('local')
                        ->visibility('private'),
                        TextEntry::make('desc')
                        ->label('Beskrivning'),
                        TextEntry::make('acquisition_date')
                        ->label('Inköpsdatum'),
                        TextEntry::make('category.name')
                        ->label('Kategori'),
                        TextEntry::make('players')
                        ->label('Antal spelare'),
                        TextEntry::make('play_time')
                        ->label('Speltid'),
                        TextEntry::make('age')
                        ->label('Ålder'),
                    ])
                    ->columns(),
                ]),
                Action::make('reserve')
                ->label('Reservera')
                ->button()
                ->color('primary')
                ->form([
                    TextInput::make('username')
                        ->label('Namn')
                        ->required(),
                    TextInput::make('email')
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
