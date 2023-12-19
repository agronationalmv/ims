<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ItemConsumptionWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'md';
    protected function getTableQuery(): Builder
    {
        return OrderDetail::with('product')
                                    ->selectRaw("product_id,sum(total) AS total")
                                    ->where('created_at','>=',now()->format('Y-m-1'))
                                    ->groupBy("product_id")
                                    ->limit(10);

    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name'),
            Tables\Columns\TextColumn::make('total'),
        ];
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        return $query;
    }

    public function getTableRecordKey($record): string{
        return 'id';
    }

}
