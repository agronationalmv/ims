<?php

namespace App\Filament\Widgets;

use App\Models\BillDetail;
use App\Models\OrderDetail;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $product_count=Product::count();
        $expense=BillDetail::where('created_at','>=',now()->format('Y-m-1'))->sum('total');
        $consumption=OrderDetail::where('created_at','>=',now()->format('Y-m-1'))->sum('total');
        
        return [
            Stat::make('Products', $product_count),
            Stat::make('Consumption This Month', $consumption),
            Stat::make('Expense This Month ', $expense),
        ];
    }
}
