<?php

namespace App\Filament\Resources\StoreResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $pluralLabel = 'Products';

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $modelLabel = 'Product';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('min_qty')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('init_qty')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('qty')
                    ->numeric()
                    ->readOnly()
                    ->default(0)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Products')
            ->columns([
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('qty'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    
}
