<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use App\Filament\Traits\HasCancelAction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPoReceive extends ViewRecord
{
    use HasCancelAction;
    protected static string $resource = PoReceiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCancelFormAction()
            // Actions\EditAction::make(),
        ];
    }
}
