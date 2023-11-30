<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Services\PurchaseOrderService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function afterSave(){
        $purchaseOrderService=app(PurchaseOrderService::class);
        $purchaseOrderService->updateTotal($this->record);
    }
    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }


}
