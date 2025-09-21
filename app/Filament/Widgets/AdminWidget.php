<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;
use App\Models\Item;
use App\Models\Reserveditem;
use Filament\Support\Enums\IconPosition;


class AdminWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [
            //Card::make(__('Total number of users'), User::count() ),
            Stat::make(__('Total amount of games'), Item::where('type', 'game')->count() ),
            Stat::make(__('Total amount of items'), Item::where('type', 'item')->count() ),
            Stat::make(__('Reservations at the moment'), Reserveditem::where('returned_date', null)->count() ),
            Stat::make(__('Reservations over time'), Reserveditem::withTrashed()->withTrashed()->count() ),
        ];
    }

    public static function canView(): bool
    {
        if (auth()->user()->is_admin==true) {
            return true;
        } else {
            return false;
        }
    }

}
