<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Database\Eloquent\Collection;

class CreatePurchaseOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = PurchaseOrderResource::class;

    protected function afterCreate(): void
    {
        $order=$this->record;
        $order->subtotal=$this->calculate_subtotal($order->items);   
        $order->total_gst=$this->calculate_gst($order->items);  
        $order->net_total=$order->subtotal+$order->total_gst;
        $order->save();
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Details')
                ->schema([
                    Section::make()->schema(PurchaseOrderResource::getFormSchema())->columns(),
                ]),

            Step::make('Items')
                ->schema([
                    Section::make()->schema(PurchaseOrderResource::getFormSchema('items')),
                ]),
        ];
    }

    public function calculate_gst(Collection $items){
        return $items->reduce(function($carry,$item){
            $carry+=$item->gst_rate*$item->qty*$item->price;
            return $carry;
        },0);
    }

    public function calculate_subtotal(Collection $items){
        return $items->reduce(function($carry,$item){
            $carry+=$item->qty*$item->price;
            return $carry;
        },0);
    }
}
