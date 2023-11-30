<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdjutsmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'adjutsments';

    protected static ?string $modelLabel = "Adjustment";
    protected static ?string $pluralModelLabel = "Adjustments";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('adjustment_type_id')
                    ->relationship('adjustment_type', 'name')
                    ->preload(true)
                    ->live()
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('qty')
                    ->label('Quantity')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        $set('total', round($get('price') * $get('qty') ?? 0, 2));
                    })
                    ->numeric($decimalPlaces = 2)
                    ->default(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('adjustment_type.name'),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('user.name')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data,RelationManager $livewire):array{
                        $product = $livewire->ownerRecord;
                        $data['product_id']=$product->id;
                        $data['user_id'] = auth()->id();
                        $price = floatVal($product?->price);
                        $data['price']=$price;
                        $total = $price * 1;
                        $data['total']=$total;
                        return $data;
                    }),
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
