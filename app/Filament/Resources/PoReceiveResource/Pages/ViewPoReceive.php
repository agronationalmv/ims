<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use App\Filament\Traits\HasCancelAction;
use App\Models\Bill;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPoReceive extends ViewRecord
{
    use HasCancelAction;
    protected static string $resource = PoReceiveResource::class;

    public ?PurchaseOrder $purchaseOrder=null;

    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Convert to Bill')
                    ->url(fn (): string => route('filament.admin.resources.bills.create',['receipt'=>$this->record->id]))
                    ->hidden(function(){
                        return Bill::where('receipt_id',$this->record->id)->exists();
                    }),
            $this->getCancelFormAction()
            // Actions\EditAction::make(),
        ];
    }
}
