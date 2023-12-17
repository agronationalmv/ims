<?php

namespace App\Services;

use App\Models\AdjustmentDetail;
use App\Models\BillDetail;
use App\Models\OrderDetail;
use App\Models\PoReceiveDetail;
use App\Models\Product;

class ProductService{

    public function updateProductBalance($store_id,$product){
        $product->stores()->where('store_id',$store_id)->update([
            'qty'=>$product->init_qty+$this->getProductBalance($store_id,$product)
        ]);
    }

    public function getProductBalance($store_id,$product){
        $in=PoReceiveDetail::whereHas('po_receive',function($q)use($store_id){
                                    $q->where('store_id',$store_id);
                                })->where('product_id',$product->id)->sum('consuming_qty');

        $out=AdjustmentDetail::whereHas('inventory_adjustment',function($q)use($store_id){
                                    $q->where('store_id',$store_id);
                                })->where('product_id',$product->id)->sum('qty');

        $out+=OrderDetail::whereHas('order',function($q)use($store_id){
                                    $q->where('store_id',$store_id);
                                })->where('product_id',$product->id)->sum('qty');
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

    public function average_price_consuming(Product $product){
        $qty=BillDetail::where('product_id',$product->id)->sum('consuming_qty');
        if($qty<=0){
            return $product->price;
        }
        $total=BillDetail::where('product_id',$product->id)->sum('total');
        return round($total/$qty,2);
    }
}