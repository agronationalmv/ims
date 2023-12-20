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
            'Date'=>'order.order_date',
            'Item'=>'product.name',
            'Qty'=>'qty',
            'Price'=>'price',
            'Total'=>'total',
        ];
    }
    

    public static function query(){
        return OrderDetail::with('order','product');
    }

    public static function filter(Builder $query): Builder{
        return $query->when(
                    static::$filters['created_from']??'',
                    fn (Builder $query, $date): Builder => 
                        $query->whereHas('order',fn(Builder $query, $date)=>$query->whereDate('order_date', '>=', $date)),
                )
                ->when(
                    static::$filters['created_until']??'',
                    fn (Builder $query): Builder=> 
                        $query->whereHas('order',fn(Builder $query, $date)=>$query->whereDate('order_date', '<=', $date)),
                );
    }
}