<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Resources\PoReceiveResource;
use App\Jobs\ProductBalanceUpdateJob;
use App\Models\PoReceive;
use App\Models\PurchaseOrder;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreatePoReceive extends CreateRecord
{
    // use HasWizard;

    public ?PurchaseOrder $purchaseOrder=null;

    protected static string $resource = PoReceiveResource::class;

    protected function afterFill(){
        if($this->purchaseOrder){
            $this->data['purchase_order_id']=$this->purchaseOrder?->id;
            $this->data['supplier_id']=$this->purchaseOrder?->supplier_id;
            $this->data['store_id']=$this->purchaseOrder?->store_id;
            $this->data['items']=$this->purchaseOrder->items->map(function($item){
                $item->qty=$item->balance;
                return $item;
            })
            ->where('qty','>',0)
            ->toArray();
        }

    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['received_by_id']=auth()->id();

        if($this->purchaseOrder){
            $data['purchase_order_id']=$this->purchaseOrder?->id;
            $data['supplier_id']=$this->purchaseOrder?->supplier_id;
            $data['store_id']=$this->purchaseOrder?->store_id;
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        if($this->record->purchase_order_id){
            return route('filament.admin.resources.purchase-orders.view', [
                'record' => $this->record?->purchase_order_id,
                'activeRelationManager'=>1
            ]);
        }
        return $this->getResource()::getUrl('view',['record'=>$this->record]);
    }

    protected function afterCreate(){
        if($this->record->purchase_order_id){
            $balance=$this->record->purchase_order->items->reduce(function($carry,$item){
                return $carry+$item->balance;
            },0);
            if($balance<=0){
                $this->record->purchase_order->status = PurchaseOrderStatus::Completed;
                $this->record->purchase_order->save();
            }
        }

        
    }

}
