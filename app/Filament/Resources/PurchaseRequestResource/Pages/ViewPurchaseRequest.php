<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Enum\PurchaseRequestStatus;
use App\Filament\Resources\PurchaseRequestResource;
use App\Filament\Traits\HasCancelAction;
use App\Models\PurchaseRequest;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseRequest extends ViewRecord
{
    use HasCancelAction;

    protected static string $resource = PurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        $actions=[];
        if($this->record->status!=PurchaseRequestStatus::Completed){
            $actions[]=$this->getPoCreateAction();
        }
        $actions[]=$this->getCancelFormAction();
        $actions[]=Actions\EditAction::make();

        return array_merge($actions,$this->getStatusActions());
    }

    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }

    private function getPoCreateAction(){
        return Actions\Action::make(__('Convert to PurchaseOrder'))
            ->url(fn (): string => route('filament.admin.resources.purchase-orders.create',['purchaseRequest'=>$this->record->id]));
    }
    private function getStatusActions(){
        $actions=[];
        foreach($this->record->status->getActions() as $label=>$status){
            $actions[]=Actions\Action::make($label)
                            ->action(function(PurchaseRequest $record)use($status){
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
