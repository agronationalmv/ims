<?php

namespace App\Reports;

use App\Models\BillDetail;
use App\Models\ExpenseAccount;
use App\Reports\Contracts\ReportContract;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;

class PurchaseReport extends ReportContract{

    protected static string $title="Purchase Report";

    public static function getColumns(){
        return [
            'Item'=>'product.name',
            'Qty'=>'qty',
            'Total'=>'total',
        ];
    }

    public static function query(){
        return BillDetail::with('product')
                            ->selectRaw('product_id,sum(qty) as qty,sum(total) as total')
                            ->groupBy('product_id');
    }

    public static function filterForm():array{
        $expense_accounts=ExpenseAccount::pluck('name','id');
        return [
            Forms\Components\Select::make('expense_account_id')
                        ->options($expense_accounts),
            Forms\Components\DatePicker::make('created_from')
                        ->default(now()->format('Y-m-1')),
            Forms\Components\DatePicker::make('created_until'),
        ];
    }

    public static function filter(Builder $query,array $data): Builder{
        return $query->when(
                        $data['created_from']??'',
                        fn (Builder $query, $date): Builder => 
                            $query->whereHas('bill',function(Builder $query)use($date){
                                $query->whereDate('bill_date', '>=', $date);
                            }),
                    )
                    ->when(
                        $data['created_until']??'',
                        fn (Builder $query,$date): Builder=> 
                            $query->whereHas('bill',function(Builder $query)use($date){
                                $query->whereDate('bill_date', '<=', $date);
                            }),
                    )
                    ->when(
                        $data['expense_account_id']??'',
                        fn (Builder $query,$expense_account_id): Builder=> 
                                $query->whereHas('bill',function(Builder $query)use($expense_account_id){
                                    $query->where('expense_account_id', $expense_account_id);
                                }),
                    );
    }

}