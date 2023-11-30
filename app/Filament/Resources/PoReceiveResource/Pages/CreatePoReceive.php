<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
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
        $po=PurchaseOrder::find($data['purchase_order_id']);
        $data['received_by_id']=auth()->id();
        $data['supplier_id']=$po->supplier_id;
        return $data;
    }
    protected function afterCreate(): void
    {
        foreach($this->record->items as $item){
            $product=$item->product;
            $product->qty=$this->product_balance($product);
            $product->save();
        }
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

    public function product_balance($product) {
        $in=Transaction::where('product_id',$product->id)->where('transaction_type','in')->sum('qty');
        $out=Transaction::where('product_id',$product->id)->where('transaction_type','out')->sum('qty');
        return $in-$out;
    }

    // protected function getSteps(): array
    // {
    //     return [
    //         Step::make('Details')
    //             ->schema([
    //                 Section::make()->schema(PoReceiveResource::getFormSchema())->columns(),
    //             ])
    //             ->afterValidation(function (Component $livewire) {
    //                 $this->data['items']=$livewire->purchaseOrder->items->map(function($item){
    //                     return ["product_id"=>$item->product_id,"qty"=>$item->qty];
    //                 })->toArray();
    //             }),

    //         Step::make('Items')
    //             ->schema([
    //                 Section::make()->schema(PoReceiveResource::getFormSchema('items')),
    //             ]),
    //     ];
    // }
}
