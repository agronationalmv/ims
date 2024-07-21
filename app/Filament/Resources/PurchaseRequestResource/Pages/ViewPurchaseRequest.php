<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Enum\PurchaseRequestStatus;
use App\Filament\Resources\PurchaseRequestResource;
use App\Filament\Traits\HasCancelAction;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseRequest extends ViewRecord
{
    use HasCancelAction;

    protected static string $resource = PurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        $actions=[];
        // if($this->record->status!=PurchaseRequestStatus::Completed){
        //     $actions[]=$this->getPoCreateAction();
        // }
        $actions[]=$this->getCancelFormAction();
        $actions[]=Actions\EditAction::make();
        $hodId = $this->record->department->hod_id; // assuming relationship is defined
        $user = Auth::user();  
        if ($user->id == $hodId ) {
            return array_merge($actions, $this->getStatusActions());
        } else {
            return $actions;
        }
    }

    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }

    // private function getPoCreateAction(){
    //     return Actions\Action::make(__('Convert to PurchaseOrder'))
    //         ->url(fn (): string => route('filament.admin.resources.purchase-orders.create',['purchaseRequest'=>$this->record->id]));
    // }
    private function getStatusActions()
    {
        $actions = [];
        
        foreach ($this->record->status->getActions() as $label => $status) {
            if ($status === PurchaseRequestStatus::Approved) {
                $actions[] = Actions\Action::make($label)
                    ->form([
                        Forms\Components\TextInput::make('budget_code')
                            ->label('Budget Code')
                            ->required(),
                    ])
                    ->action(function (array $data) use ($status) {
                        $this->record->budget_code = $data['budget_code'];
                        $this->record->status = $status->value;
                        $this->record->save();
                    })
                    ->modalDescription("Are you sure you want to approve this request?")
                    ->requiresConfirmation()
                    ->color($status->getColor());
            } else {
                $actions[] = Actions\Action::make($label)
                    ->action(function (PurchaseRequest $record) use ($status) {
                        $record->status = $status->value;
                        $record->save();
                    })
                    ->modalDescription("Are you sure?")
                    ->requiresConfirmation()
                    ->color($status->getColor());
            }
        }

        return $actions;
    }
}
