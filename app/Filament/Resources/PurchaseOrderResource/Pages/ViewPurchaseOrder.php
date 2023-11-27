<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Filament\Traits\HasCancelAction;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    use HasCancelAction;
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([
            $this->getCancelFormAction(),
            Actions\EditAction::make(),
        ],$this->getStatusActions());
    }

    private function getStatusActions(){
        $actions=[];
        foreach($this->record->status->getActions() as $label=>$status){
            $actions[]=Actions\Action::make($label)
                            ->action(function(PurchaseOrder $record)use($status){
                                $record->status=$status->value;
                                $record->save();
                            })
                            ->modalDescription("Are you sure?")
                            ->requiresConfirmation()
                            ->color($status->getColor());
        }
        return $actions;
    }
}
