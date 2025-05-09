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
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Notifications\Notification;

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
                Grid::make()
                ->columns(1)
                ->schema([
                    Stack::make([
                        TextColumn::make('reserved')
                        ->weight(FontWeight::Bold)
                        ->formatStateUsing(fn (string $state): string => $state ? 'Utlånad' : 'Tillgänglig')
                        ->color(fn($record) => $record->reserved ? 'danger' : 'success' )
                        ->badge(),
                        ImageColumn::make('image')
                        ->label('Bild')
                        ->disk('local')
                        ->size('100%')
                        ->extraImgAttributes([
                            'class' => 'rounded-md'
                        ])
                        ->visibility('private'),
                        TextColumn::make('desc')
                        ->label('Beskrivning')
                        ->sortable()
                        ->searchable()
                        ->weight(FontWeight::Bold)
                        ->size(TextColumn\TextColumnSize::Large),
                        Panel::make([
                            Stack::make([
                                TextColumn::make('players')
                                    ->label('Antal spelare')
                                    ->sortable()
                                    ->icon('heroicon-m-users'),
                                TextColumn::make('play_time')
                                    ->label('Speltid')
                                    ->sortable()
                                    ->icon('heroicon-m-clock'),
                                TextColumn::make('age')
                                    ->label('Ålder')
                                    ->sortable()
                                    ->icon('heroicon-m-arrows-right-left'),
                            ])->extraAttributes(['class' => 'space-y-3'])                           
                        ])
                    ])->extraAttributes(['class' => 'space-y-3'])  
                    
                ])
            ])
            ->defaultSort('desc', 'asc')
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
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
                    Notification::make()
                    ->title('Spelet är reserverat!')
                    ->body('Ett bekräftelsemail har skickats till emailadressen du uppgav. Läs det för mer info om utlämning av spelet.')
                    ->success()
                    ->seconds(10)
                    ->send();
                })
                ->hidden(fn ($record) => $record->reserved),
                Action::make('Mer info')
                ->modalSubmitAction(false)  
                ->button()
                ->color('primary') 
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
            ])
            ->bulkActions([
            ]);

            
    }
}
