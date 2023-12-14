<?php

namespace App\Filament\Resources\ExpenseAccountResource\Pages;

use App\Filament\Resources\ExpenseAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageExpenseAccounts extends ManageRecords
{
    protected static string $resource = ExpenseAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
