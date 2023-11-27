<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoReceive extends EditRecord
{
    protected static string $resource = PoReceiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
