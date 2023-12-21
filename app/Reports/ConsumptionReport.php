<?php

namespace App\Reports;

use App\Models\BillDetail;
use App\Models\OrderDetail;
use App\Reports\Contracts\ReportContract;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;

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
    public static function filterForm():array{
        return [
            Forms\Components\DatePicker::make('created_from')
                ->default(now()->format('Y-m-1')),
            Forms\Components\DatePicker::make('created_until'),
        ];
    }

    public static function filter(Builder $query,array $data): Builder{
        return $query->when(
                            $data['created_from']??'',
                            fn (Builder $query, $date): Builder => 
                                $query->whereHas('order',function(Builder $query)use($date){
                                    $query->whereDate('order_date', '>=', $date);
                                }),
                        )
                        ->when(
                            $data['created_until']??'',
                            fn (Builder $query,$date): Builder=> 
                                $query->whereHas('order',function(Builder $query)use($date){
                                    $query->whereDate('order_date', '<=', $date);
                                }),
                        );
    }
}