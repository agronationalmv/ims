<?php

namespace App\Filament\Resources\PoReceiveResource\Pages;

use App\Filament\Resources\PoReceiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoReceives extends ListRecords
{
    protected static string $resource = PoReceiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
