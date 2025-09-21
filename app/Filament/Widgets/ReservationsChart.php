<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Reserveditem;

class ReservationsChart extends ChartWidget
{
    protected static ?string $heading = "Reservations by month";
    protected static ?string $maxHeight = '300px';
    
    
    protected function getData(): array
    {


       $data = Trend::query( Reserveditem::query()->withTrashed())
        ->dateColumn('reserved_date')
        ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
        ->perMonth()
        ->count();

         return [
            'datasets' => [
                [
                    'label' => __('Reservations'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

   

    protected function getType(): string
    {
        return 'line';
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
