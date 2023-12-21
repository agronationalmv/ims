<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Enum\BillStatus;
use App\Filament\Resources\BillResource;
use App\Models\Bill;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Paid')
                ->action(function(Bill $record){
                    $record->status=BillStatus::Paid->value;
                    $record->save();
                })
                ->visible(fn(Bill $record)=>$record->status==BillStatus::Unpaid)
                ->modalDescription("Are you sure?")
                ->requiresConfirmation()
                ->color(BillStatus::Paid->getColor()),
            Actions\EditAction::make(),
        ];
    }

    protected function afterFill(){
        $this->data['items']=$this->record->items()->with('product','product.unit')->get()->toArray();
    }

    
}
