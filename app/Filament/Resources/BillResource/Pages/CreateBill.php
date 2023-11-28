<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function afterCreate() : void {
        foreach($this->record->items as $item){
            $product=$item->product;
            $product->price=$this->average_price($product);
            $product->save();
        }
    }

    public function average_price(Product $product){
        $qty=BillDetail::where('product_id',$product->id)->sum('qty');
        $total=BillDetail::where('product_id',$product->id)->sum('total');
        return round($total/$qty,2);
    }
}
