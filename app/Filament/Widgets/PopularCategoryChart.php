<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Reserveditem;
use App\Models\Category;
use App\Models\Item;


class PopularCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Game reservations by category';
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
        $categories = Category::where('type', 'game')->get()->sortBy('id')->pluck('name');

        $items = Reserveditem::withTrashed()->with('item')->get()->groupBy('item.category_id')->sortByDesc('item.category_id');
        $itemCategoriesCount = [];

        foreach ($items as $item){
            $item1 = Item::where('id', $item[0]->item_id)->get();
            $type = Category::where('id', $item1[0]->category_id)->get();
            if ($type[0]->type === "game") {
                 $itemCategoriesCount[] = count($item);
            }
           
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
