<?php

namespace App\Filament\Resources\AdjustmentTypeResource\Pages;

use App\Filament\Resources\AdjustmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAdjustmentTypes extends ManageRecords
{
    protected static string $resource = AdjustmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
