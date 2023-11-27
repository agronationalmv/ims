<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function getBalanceAttribute(){
        return $this->qty - PoReceiveDetail::whereHas('po_receive',function($q){
                                                $q->where('purchase_order_id',$this->purchase_order_id);
                                            })->sum('qty');
    }
    
}
