<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\ProductStore;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowInventory extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'md';
    public function table(Table $table): Table
    {
        return $table
                ->query(ProductStore::whereRaw('qty<min_qty'))
                ->columns([
                    Tables\Columns\TextColumn::make('store.name'),
                    Tables\Columns\TextColumn::make('product.name'),
                    Tables\Columns\TextColumn::make('product.uoc.name')
                        ->label('Unit'),
                    Tables\Columns\TextColumn::make('min_qty')
                        ->label('Alert level'),
                    Tables\Columns\TextColumn::make('qty')
                        ->label('balance')
                ]);
    }
}
