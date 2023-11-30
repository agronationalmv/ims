<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterFill(){
        $this->data['requested_by_id']=auth()->id();
    }
    protected function afterCreate(): void
    {
        foreach($this->record->items as $item){
            $product=$item->product;
            $product->qty=$this->product_balance($product);
            $product->save();
        }
    }

    public function product_balance($product) {
        $in=Transaction::where('product_id',$product->id)->where('transaction_type','in')->sum('qty');
        $out=Transaction::where('product_id',$product->id)->where('transaction_type','out')->sum('qty');
        return $in-$out;
    }
}
