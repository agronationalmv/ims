<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function purchase_request() : BelongsTo {
        return $this->belongsTo(PurchaseRequest::class);
    }
    
    public function getBalanceAttribute(){
        return $this->qty - $this->received;
    }

    public function getReceivedAttribute(){
        return PurchaseOrderDetail::whereHas('purchase_order',function($q){
                                    $q->where('purchase_request_id',$this->purchase_request_id);
                                })
                                ->where('product_id',$this->product_id)
                                ->sum('qty');
    }

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }

}
