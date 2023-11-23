<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $product_count=Product::count();
        $low_inventory_count=Product::whereRaw('qty<=min_qty')->count();
        
        return [
            Stat::make('Products', $product_count),
            Stat::make('Low Inventory', $low_inventory_count),
        ];
    }
}
