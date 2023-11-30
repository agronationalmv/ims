<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use App\Filament\Traits\HasCancelAction;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPoReceive extends ViewRecord
{
    use HasCancelAction;
    protected static string $resource = PoReceiveResource::class;

    public ?PurchaseOrder $purchaseOrder=null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Convert to Bill')
                    ->url(fn (): string => route('filament.admin.resources.bills.create',['receipt'=>$this->record->id])),
            $this->getCancelFormAction()
            // Actions\EditAction::make(),
        ];
    }
}
