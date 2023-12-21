<?php

namespace App\Filament\Widgets;

use App\Models\BillDetail;
use App\Models\ExpenseAccount;
use App\Models\OrderDetail;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $expense=BillDetail::whereHas('bill',function($q){
                $q->where('bill_date','>=',now()->format('Y-m-1'));
        })->sum('total');
        $consumption=OrderDetail::where('created_at','>=',now()->format('Y-m-1'))->sum('total');
        
        $widgets=[
            Stat::make('Total Consumption', "MVR ".$consumption)
                ->description("Current Month"),
            Stat::make('Total Expense', "MVR ".$expense)
                ->description("Current Month"),
        ];

        foreach(ExpenseAccount::all() as $expense_account){
            $expense=BillDetail::whereHas('bill',function($q)use($expense_account){
                $q->where('expense_account_id',$expense_account->id)
                        ->where('bill_date','>=',now()->format('Y-m-1'));
            })->sum('total');
            $widgets[]=Stat::make($expense_account->name." Expense", "MVR ".$expense)->description("Current Month");
        }
        return $widgets;
    }
}
