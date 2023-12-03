<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static bool $canCreateAnother=false;

    protected function afterFill(){
        $this->data['requested_by_id']=auth()->id();
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Order Details')
                ->schema(
                    OrderResource::getFormSchema()
                ),

            Step::make('Order Items')
                ->schema(
                    OrderResource::getFormSchema('items'),
                ),
        ];
    }
    
}
