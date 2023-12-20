<?php

namespace App\Reports;

use App\Models\BillDetail;
use App\Reports\Contracts\ReportContract;
use Illuminate\Database\Eloquent\Builder;

class PurchaseReport extends ReportContract{

    protected static string $title="Purchase Report";

    public static function getColumns(){
        return [
            'Date'=>'bill.bill_date',
            'Item'=>'product.name',
            'Invoice No'=>'bill.reference_no',
            'Expense Account'=>'bill.expense_account.name',
            'Qty'=>'qty',
            'Price'=>'price',
            'Gst'=>'gst_rate',
            'Total'=>'total',
        ];
    }

    public static function query(){
        return BillDetail::with('bill','product','bill.expense_account');
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