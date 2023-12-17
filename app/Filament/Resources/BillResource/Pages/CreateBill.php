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
            $this->data['expense_account_id']=$this->receipt?->purchase_order?->expense_account_id;
            $this->data['items']=$this->receipt->items()->with('product','product.unit')->get()->toArray();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if($this->receipt){
            $data['receipt_id']=$this->receipt?->id;
            $data['purchase_order_id']=$this->receipt?->purchase_order_id;
            $data['supplier_id']=$this->receipt?->supplier_id;
            $data['expense_account_id']=$this->receipt?->purchase_order?->expense_account_id;
        }  

        return $data; 
    }
    protected function afterCreate() : void {
        $productService=app(ProductService::class);
        $this->record->subtotal=0;
        $this->record->total_gst=0;
        $this->record->net_total=0;

        foreach($this->record->items as $item){
            $product=$item->product;
            $product->price=$productService->average_price($product);
            $product->price_each=$productService->average_price_consuming($product);
            $product->save();
            $this->record->subtotal=$item->qty*$item->price;
            $this->record->total_gst=$item->gst_rate*$item->price;
            $this->record->net_total=$item->qty*$item->price*(1+$item->gst_rate);
        }
        $this->record->save();
    }

}
