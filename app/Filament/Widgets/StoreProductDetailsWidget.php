<?php

namespace App\Filament\Widgets;

use App\Models\ProductStore;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StoreProductDetailsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Currunt Stock Level';

    protected function getTableDescription(): string|Htmlable|null
    {
        return 'Total Stock Level';
    }

    protected function getTableQuery(): Builder
    {
        return ProductStore::query()
            ->join('stores', 'product_stores.store_id', '=', 'stores.id')
            ->join('products', 'product_stores.product_id', '=', 'products.id')
            ->select('product_stores.*', 'stores.name as store_name', 'products.name as product_name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('store_name')
                ->label('Store Name')
                ->sortable()
                ->searchable(query: function (Builder $query, string $search) {
                    return $query->where('stores.name', 'like', "%{$search}%");
                }),
            Tables\Columns\TextColumn::make('product_name')
                ->label('Product Name')
                ->sortable()
                ->searchable(query: function (Builder $query, string $search) {
                    return $query->where('products.name', 'like', "%{$search}%");
                }),
            Tables\Columns\TextColumn::make('qty')
                ->label('Quantity')
                ->sortable()
                ->searchable(query: function (Builder $query, string $search) {
                    return $query->where('product_stores.qty', 'like', "%{$search}%");
                })
                ->formatStateUsing(function ($state) {
                    return number_format($state, 0);
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
