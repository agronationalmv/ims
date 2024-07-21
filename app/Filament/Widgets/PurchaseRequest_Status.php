<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\PurchaseRequest;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PurchaseRequest_Status extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 99;
    protected int | string | array $columnSpan = 'md';
    protected static ?string $heading = 'Purchase Requests Status';



    protected function getTableQuery(): Builder
    {
        return PurchaseRequest::query()
            ->join('stores', 'purchase_requests.store_id', '=', 'stores.id')
            ->select('purchase_requests.status as status', 'purchase_requests.reference_no as pr_no', 'stores.name as store_name')
            ->orderByRaw('CASE WHEN purchase_requests.status = "approved" THEN 0 ELSE 1 END') 
            ->orderBy('purchase_requests.reference_no'); 
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('store_name')
                ->label('Store Name')
                ->sortable(),

            Tables\Columns\TextColumn::make('pr_no')
                ->label('PR Number')
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->formatStateUsing(function ($state) {
                    return $state->value == 'approved' ? 'Active' : 'Closed';
                })
                ->color(function ($state) {
                    return $state->value == 'approved' ? 'green' : 'red';
                }),
        ];
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        return $query;
    }

    public function getTableRecordKey($record): string
    {
        return 'id';
    }
}
