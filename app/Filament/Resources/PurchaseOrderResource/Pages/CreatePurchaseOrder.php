<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
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


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['subtotal']=0;
        $data['total_gst']=0;
        $data['total']=0;

        foreach($data['items'] as $itemLine){
            $data['subtotal']+=$itemLine->price*$itemLine->qty;
            $data['total_gst']+=$itemLine->gst_rate*$itemLine->price;
            $data['total']+=($itemLine->gst_rate+1)*$itemLine->price*$itemLine->qty;
        }
        return $data; 
    }

    protected function afterCreate(): void
    {
        $purchaseOrderService=app(PurchaseOrderService::class);
        $purchaseOrderService->updateTotal($this->record);
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
