<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;
use App\Models\Item;
use App\Models\Reserveditem;

class AdminWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [
            //Card::make(__('Total number of users'), User::count() ),
            Card::make(__('Total number of games'), Item::where('type', 'game')->count() ),
            Card::make(__('Total number of items'), Item::where('type', 'item')->count() ),
            Card::make(__('Reserved games and items'), Reserveditem::count() ),
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
