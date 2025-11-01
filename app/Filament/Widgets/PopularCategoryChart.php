<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Reserveditem;
use App\Models\Category;
use App\Models\Item;


class PopularCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Reservations by category';
    protected static ?string $maxHeight = '275px';

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'display' => false,
            ],
            'y' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $categories = Category::get()->sortBy('id')->pluck('name');

        $items = Reserveditem::withTrashed()->with('item')->get()->groupBy('item.category_id')->sortByDesc('item.category_id');
        $itemCategoriesCount = [];

        foreach ($items as $item){
            $itemCategoriesCount[] = count($item);
        }

        return [
            'datasets' => [
                [
                    'label' => __('Reservation Category'),
                    'data' => $itemCategoriesCount,
                    'backgroundColor' => ["#03045e","#0077b6","#00b4d8","#90e0ef","#caf0f8"],
                    "hoverOffset" => 4,
                ],
            ],
            'labels' => $categories,
            
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
