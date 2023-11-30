<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function getBalanceAttribute(){
        return $this->qty - $this->received;
    }

    public function getReceivedAttribute(){
        return PoReceiveDetail::whereHas('po_receive',function($q){
                                    $q->where('purchase_order_id',$this->purchase_order_id);
                                })
                                ->where('product_id',$this->product_id)
                                ->sum('qty');
    }

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }
    
}
