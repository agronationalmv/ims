<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ItemConsumptionWidget extends BaseWidget
{
    use HasWidgetShield;
    
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'md';

    protected function getTableDescription(): string|Htmlable|null
    {
        return 'Current Month';   
    }

    protected function getTableQuery(): Builder
    {
        return OrderDetail::with('product')
                                    ->selectRaw("product_id,sum(qty) AS qty")
                                    ->where('created_at','>=',now()->format('Y-m-1'))
                                    ->groupBy("product_id");

    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name'),
            Tables\Columns\TextColumn::make('qty'),
            Tables\Columns\TextColumn::make('product.uoc.name'),

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
