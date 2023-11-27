<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Collection;

class PurchaseOrderService{

    public function updateTotal(PurchaseOrder $purchaseOrder){
        $purchaseOrder->subtotal=$this->calculate_subtotal($purchaseOrder->items);   
        $purchaseOrder->total_gst=$this->calculate_gst($purchaseOrder->items);  
        $purchaseOrder->net_total=$purchaseOrder->subtotal+$purchaseOrder->total_gst;
        $purchaseOrder->save();
    }
    public function calculate_gst(Collection $items){
        return $items->reduce(function($carry,$item){
            $carry+=$item->gst_rate*$item->qty*$item->price;
            return $carry;
        },0);
    }

    public function calculate_subtotal(Collection $items){
        return $items->reduce(function($carry,$item){
            $carry+=$item->qty*$item->price;
            return $carry;
        },0);
    }
}