<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoReceive extends EditRecord
{
    public ?PurchaseOrder $purchaseOrder=null;

    protected static string $resource = PoReceiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }
}
