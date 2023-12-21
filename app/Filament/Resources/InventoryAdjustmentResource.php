<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryAdjustmentResource\Pages;
use App\Filament\Resources\InventoryAdjustmentResource\RelationManagers;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\ProductStore;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryAdjustmentResource extends Resource
{
    protected static ?int $navigationSort = 7;

    protected static ?string $model = InventoryAdjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Group::make()
            ->schema([
                Forms\Components\Section::make()
                    ->schema(static::getFormSchema())
                    ->columns(2)
            ])
            ->columnSpan(['lg' => 1]),
            Forms\Components\Section::make()
                ->schema(static::getFormSchema('items'))
                ->columnSpan(['lg' => 1]),

        ])
        ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adjustment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('store.name'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('adjustment_type.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryAdjustments::route('/'),
            'create' => Pages\CreateInventoryAdjustment::route('/create'),
            'view' => Pages\ViewInventoryAdjustment::route('/{record}'),
            'edit' => Pages\EditInventoryAdjustment::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(string $section = null): array
    {
        if($section=='items'){
            return [
                Forms\Components\Repeater::make('items')
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Product')
                        ->options(function(Forms\Get $get){
                            return Product::query()->whereHas('stores',function($q)use($get){
                                $q->where('store_id',$get('../../store_id'));
                            })->pluck('name', 'id');

                        })
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $product=Product::find($get('product_id'));
                            $product->uoc;
                            $set('product',$product);
                        })
                        ->required()
                        ->live()
                        ->columnSpan([
                            'md' => 5,
                        ])
                        ->searchable(),
                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->prefix(fn(Forms\Get $get)=>$get('product.uoc.name'))
                        ->suffix(function(Forms\Get $get){
                            $product=ProductStore::where('store_id',$get('../../store_id'))
                                            ->where('product_id',$get('product_id'))
                                            ->first();
                            return $product?$product->qty:0;
                        })
                        ->numeric($decimalPlaces=2)
                        ->default(1)
                        ->rules([
                            fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                $product=$get('product');

                                $productStore=ProductStore::where('store_id',$get('../../store_id'))
                                                ->where('product_id',$get('product_id'))
                                                ->first();

                                if($productStore){
                                    if($value>floatval($productStore->qty)){
                                        $fail("The quantity must be less than or equal to {$productStore->qty}");
                                    }
                                }

                                if($value<=0){
                                    $fail("The quantity must be less than 0");
                                }

                            },
                        ])
                        ->gt(0)
                        ->columnSpan([
                            'md' => 2,
                        ])
                        ->required()
                ])
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $product=Product::find($data['product_id']);
                    $price=floatVal($product?->price);
                    $qty=floatVal($data['qty']);
                    $data['price']=$price;
                    $data['total']=$price*$qty;
                    return $data;
                })
                ->defaultItems(1)
                ->columns([
                    'md' => 10,
                ])
                ->required()
            ];
        }

        return [
            Forms\Components\TextInput::make('reference_no')
                ->required()
                ->visible(fn($operation)=>$operation=='view')
                ->maxLength(255),
            Forms\Components\DatePicker::make('adjustment_date')
                ->format('Y-m-d')
                ->native(false)
                ->default(now())
                ->required(),
            Forms\Components\Select::make('store_id')
                ->relationship('store', 'name')
                ->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            Forms\Components\Select::make('adjustment_type_id')
                ->relationship('adjustment_type', 'name')
                ->required(),
        ]; 
    }
}
