<?php

namespace App\Reports;

use App\Models\BillDetail;
use App\Models\OrderDetail;
use App\Reports\Contracts\ReportContract;
use Illuminate\Database\Eloquent\Builder;

class ConsumptionReport extends ReportContract{

    protected static string $title="Consumption Report";

    public static function getColumns(){
        return [
            'Item'=>'product.name',
            'Qty'=>'qty',
            'Total'=>'total',
        ];
    }
    

    public static function query(){
        return OrderDetail::with('product')
                            ->selectRaw('product_id,sum(qty) as qty,sum(total) as total')
                            ->groupBy('product_id');
    }

    public static function filter(Builder $query,array $filters): Builder{
        return $query->when(
                            $filters['created_from']??'',
                            fn (Builder $query, $date): Builder => 
                                $query->whereHas('order',function(Builder $query)use($date){
                                    $query->whereDate('order_date', '>=', $date);
                                }),
                        )
                        ->when(
                            $filters['created_until']??'',
                            fn (Builder $query,$date): Builder=> 
                                $query->whereHas('order',function(Builder $query)use($date){
                                    $query->whereDate('order_date', '<=', $date);
                                }),
                        );
    }
}