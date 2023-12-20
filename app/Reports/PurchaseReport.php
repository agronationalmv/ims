<?php

namespace App\Reports;

use App\Models\BillDetail;
use App\Reports\Contracts\ReportContract;
use Illuminate\Database\Eloquent\Builder;

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

    public static function filter(Builder $query): Builder{
        return $query->when(
                    static::$filters['created_from']??'',
                    fn (Builder $query, $date): Builder => 
                        $query->whereHas('bill',fn(Builder $query, $date)=>$query->whereDate('bill_date', '>=', $date)),
                )
                ->when(
                    static::$filters['created_until']??'',
                    fn (Builder $query, $date): Builder => 
                        $query->whereHas('bill',fn(Builder $query, $date)=>$query->whereDate('bill_date', '<=', $date)),
                );
    }

}