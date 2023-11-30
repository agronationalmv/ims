<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\PoReceive;
use App\Models\Product;
use App\Services\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    public ?PoReceive $receipt=null;

    protected function afterFill(){
        if($this->receipt){
            $this->data['receipt_id']=$this->receipt?->id;
            $this->data['supplier_id']=$this->receipt?->supplier_id;
            $this->data['items']=$this->receipt->items()->with('product','product.unit')->get()->toArray();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if($this->receipt){
            $data['receipt_id']=$this->receipt?->id;
            $data['purchase_order_id']=$this->receipt?->purchase_order_id;
            $data['supplier_id']=$this->receipt?->supplier_id;
        }  
        $data['subtotal']=0;
        $data['total_gst']=0;
        $data['total']=0;

        foreach($data['items'] as $itemLine){
            $data['subtotal']+=$itemLine->price*$itemLine->qty;
            $data['total_gst']+=$itemLine->gst_rate*$itemLine->price;
            $data['total']+=($itemLine->gst_rate+1)*$itemLine->price*$itemLine->qty;
        }
        return $data; 
    }
    protected function afterCreate() : void {
        $productService=app(ProductService::class);
        foreach($this->record->items as $item){
            $product=$item->product;
            $product->price=$productService->average_price($product);
            $product->save();
        }
    }

}
