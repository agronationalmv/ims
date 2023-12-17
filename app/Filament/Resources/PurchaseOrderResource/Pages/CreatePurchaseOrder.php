<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Enum\PurchaseRequestStatus;
use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseRequest;
use App\Services\PurchaseOrderService;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Database\Eloquent\Collection;

class CreatePurchaseOrder extends CreateRecord
{
    // use HasWizard;

    protected static string $resource = PurchaseOrderResource::class;

    public ?PurchaseRequest $purchaseRequest=null;

    protected function afterFill(){
        if($this->purchaseRequest){
            $this->data['purchase_request_reference_no']=$this->purchaseRequest?->reference_no;
            $this->data['purchase_request_id']=$this->purchaseRequest?->id;
            $this->data['expense_account_id']=$this->purchaseRequest?->expense_account_id;
            $this->data['department_id']=$this->purchaseRequest?->department_id;
            $this->data['store_id']=$this->purchaseRequest?->store_id;
            $this->data['items']=$this->purchaseRequest->items->map(function($item){
                $item->qty=$item->balance;
                return $item;
            })
            ->where('qty','>',0)
            ->toArray();
        }

    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['received_by_id']=auth()->id();

        if($this->purchaseRequest){
            $data['purchase_request_id']=$this->purchaseRequest?->id;
            $data['supplier_id']=$this->purchaseRequest?->supplier_id;
            $data['store_id']=$this->purchaseRequest?->store_id;
            $data['expense_account_id']=$this->purchaseRequest?->expense_account_id;
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $purchaseOrderService=app(PurchaseOrderService::class);
        $purchaseOrderService->updateTotal($this->record);

        if($this->record->purchase_request_id){
            $balance=$this->record->purchase_request->items->reduce(function($carry,$item){
                return $carry+$item->balance;
            },0);
            if($balance<=0){
                $this->record->purchase_request->update(['status'=>PurchaseRequestStatus::Completed->value]);
            }
        }
    }

    // protected function getSteps(): array
    // {
    //     return [
    //         Step::make('Details')
    //             ->schema([
    //                 Section::make()->schema(PurchaseOrderResource::getFormSchema())->columns(),
    //             ]),

    //         Step::make('Items')
    //             ->schema([
    //                 Section::make()->schema(PurchaseOrderResource::getFormSchema('items')),
    //             ]),
    //     ];
    // }
}
