<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class DailyConsumptionWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'md';

    protected function getTableDescription(): string|Htmlable|null
    {
        return 'Current Month';   
    }

    protected function getTableQuery(): Builder
    {
        return OrderDetail::selectRaw("DATE_FORMAT(created_at,'%Y-%c-%d') AS order_date,sum(total) AS total")->groupBy("order_date");

    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order_date'),
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
