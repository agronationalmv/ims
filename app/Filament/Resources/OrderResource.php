<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?int $navigationSort = 3;

    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'Stock Issue';
    protected static ?string $pluralModelLabel = 'Stock Issues';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema(static::getFormSchema('items'))
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getFormSchema())
                            ->columns(1)
                    ])
                    ->columnSpan(['lg' => 1])

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('requested_by.name')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
                        ->options(Product::query()->pluck('name', 'id'))
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $product=Product::find($get('product_id'));
                            $product->unit;
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
                        ->prefix(fn(Forms\Get $get)=>$get('product.unit.name'))
                        ->suffix(fn(Forms\Get $get)=>$get('product.qty'))
                        ->numeric($decimalPlaces=2)
                        ->default(1)
                        ->rules([
                            fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                $product=$get('product');
                                if(!$product && $get('product_id')){
                                    $product=Product::find($get('product_id'));
                                }

                                if($product){
                                    if($value>floatval($product->qty)){
                                        $fail("The quantity must be less than or equal to {$product->qty}");
                                    }
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
                ->disabled()
                ->visible(fn(?string $operation)=>$operation=='view'),
            Forms\Components\DatePicker::make('order_date')
                ->format('Y-m-d')
                ->native(false)
                ->default(now())
                ->required(),
            Forms\Components\Select::make('requested_by_id')
                ->label('Requested By')
                ->options(User::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
        ]; 
    }
}
