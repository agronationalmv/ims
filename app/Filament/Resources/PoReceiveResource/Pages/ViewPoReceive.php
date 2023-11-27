<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPoReceive extends ViewRecord
{
    protected static string $resource = PoReceiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
