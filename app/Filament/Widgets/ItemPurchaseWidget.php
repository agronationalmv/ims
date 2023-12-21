<?php

namespace App\Filament\Widgets;

use App\Models\BillDetail;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ItemPurchaseWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'md';

    protected function getTableDescription(): string|Htmlable|null
    {
        return 'Current Month';   
    }

    protected function getTableQuery(): Builder
    {
        return BillDetail::with('product')
                                    ->selectRaw("product_id,sum(qty) AS qty,sum(total) AS total")
                                    ->where('created_at','>=',now()->format('Y-m-1'))
                                    ->groupBy("product_id");

    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name'),
            Tables\Columns\TextColumn::make('product.unit.name'),
            Tables\Columns\TextColumn::make('qty'),
            Tables\Columns\TextColumn::make('total')
                        ->summarize(Sum::make()->label('Total')),
            Tables\Columns\TextColumn::make('avg_price')
                                    ->getStateUsing(fn(BillDetail $record)=>($record->total/$record->qty)??0),
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
