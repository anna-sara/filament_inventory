<?php

namespace App\Filament\Pages;

use Filament\Pages\BasePage;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use App\Models\Reserveditem;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCreatedUser;
use App\Mail\ReservationCreated;
use Filament\Forms\Components\Checkbox;


class Reserve extends BasePage implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.reserve';

    protected static ?string $title = "Reservera spel | vBytes Inventory";

    
    public static function table(Table $table): Table
    {
        return $table
            ->query( Item::where('can_be_loaned', true)->whereIn('type', ["game", "literature"]))
            ->emptyStateHeading('Inga resultat')
            ->searchable(true)
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
                        ->size('100%')
                        ->extraImgAttributes([
                            'class' => 'rounded-md',
                            'loading' => 'lazy'
                        ]),
                        //->visibility('private'),
                        TextColumn::make('desc')
                        ->label('Beskrivning')
                        ->sortable()
                        ->searchable()
                        ->weight(FontWeight::Bold)
                        ->size(TextColumn\TextColumnSize::Large),
                        Stack::make([
                            TextColumn::make('players')
                                ->label('Antal spelare')
                                ->default('Ingen uppgift')
                                ->sortable()
                                ->prefix('Spelare: '),
                                //->suffix(' st'),
                            TextColumn::make('play_time')
                                ->label('Speltid')
                                ->sortable()
                                ->default('Ingen uppgift')
                                ->prefix('Speltid (min): '),
                                //->suffix(' min'),
                            TextColumn::make('age')
                                ->label('Ålder')
                                ->sortable()
                                ->default('Ingen uppgift')
                                ->prefix('Ålder: ')
                                //->suffix(' år'),
                        ])
                        ->extraAttributes(['class' => 'space-y-3 h-full'])
                        ->hidden(fn ($record) => $record->type === 'literature'),                     
                    ])->extraAttributes(['class' => 'space-y-3 h-full'])  
                    
                ])
            ])
            //->defaultSort('desc', 'asc')
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('reserved', 'asc')
                    ->orderBy('desc', 'asc');
                   
            })
            ->defaultPaginationPageOption(12)
            ->paginated([12, 25, 50, 100, 'all'])
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
            ])
            ->filters([
                Filter::make('filters')
                ->form([
                    Select::make('type')
                        ->label('Typ')
                        ->live()
                        ->options([
                            'game' => __('Game'),
                            'literature' => __('Literature')
                        ]),
                    Select::make('category_id')
                        ->multiple()
                        ->label('Kategorier Litteratur')
                        ->options(Category::all()->whereIn('type', 'literature')->pluck('name', 'id'))
                        
                        ->hidden(fn ($get): string   => $get('type') == 'game' || $get('type') == ''),
                    Select::make('category_id')
                        ->multiple()
                        ->label('Kategorier Spel')
                        ->options(Category::all()->whereIn('type', 'game')->pluck('name', 'id'))
                        ->hidden(fn ($get): string  => $get('type') == 'literature' || $get('type') == ''),
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
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->label('Telefonnummer')
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
                    Notification::make()
                    ->title('Spelet är reserverat!')
                    ->body('Ett bekräftelsemail har skickats till emailadressen du uppgav. Läs det för mer info om utlämning av spelet.')
                    ->success()
                    ->seconds(10)
                    ->send();
                })
                ->hidden(fn ($record) => $record->reserved),
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
                    ->hidden(fn ($record) =>  $record->type === 'literature'),
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
                    ->hidden(fn ($record) => $record->type === "game"),
                ]),
            ])
            ->bulkActions([
            ]);

            
    }
}
