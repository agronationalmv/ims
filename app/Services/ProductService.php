<?php

namespace App\Services;

use App\Models\AdjustmentDetail;
use App\Models\BillDetail;
use App\Models\OrderDetail;
use App\Models\PoReceiveDetail;
use App\Models\Product;

class ProductService{

    public function updateProductBalance($product){
        $product->update([
            'qty'=>$product->init_qty+$this->getProductBalance($product)
        ]);
    }

    public function getProductBalance($product){
        $in=PoReceiveDetail::where('product_id',$product->id)->sum('qty');
        $out=AdjustmentDetail::where('product_id',$product->id)->sum('qty');
        $out+=OrderDetail::where('product_id',$product->id)->sum('qty');
        return $in-$out;
    }

    public function average_price(Product $product){
        $qty=BillDetail::where('product_id',$product->id)->sum('qty');
        if($qty<=0){
            return $product->price;
        }
        $total=BillDetail::where('product_id',$product->id)->sum('total');
        return round($total/$qty,2);
    }
}