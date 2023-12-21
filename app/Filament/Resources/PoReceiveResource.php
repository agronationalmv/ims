<?php

namespace App\Filament\Resources;

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Resources\PoReceiveResource\Pages;
use App\Filament\Resources\PoReceiveResource\RelationManagers;
use App\Models\PoReceive;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;

class PoReceiveResource extends Resource
{
    protected static ?int $navigationSort = 5;

    protected static ?string $model = PoReceive::class;

    protected ?PurchaseOrder $purchaseOrder = null;

    protected static ?string $modelLabel = 'Item Receipt';
    protected static ?string $pluralModelLabel = 'Item Receipts';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make()
                            ->schema(static::getFormSchema('items')),
                    ])
                    ->columnSpan(['lg' => fn (?string $operation) => $operation == 'view' ? 2 : 3]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_order.reference_no')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPoReceives::route('/'),
            'create' => Pages\CreatePoReceive::route('/create/{purchaseOrder?}'),
            'view' => Pages\ViewPoReceive::route('/{record}'),
            'edit' => Pages\EditPoReceive::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(string $section = null): array
    {
        if ($section == 'items') {
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
    
                            })->required()
                            ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                                $product=Product::find($get('product_id'));
                                $product->unit;
                                $set('product',$product);
                            })
                            ->afterStateHydrated(function(Forms\Get $get, Forms\Set $set){
                                if($get('product_id')){
                                    $product=Product::find($get('product_id'));
                                    $product->unit;
                                    $set('product',$product);   
                                }
                            })
                            ->live()
                            ->columnSpan([
                                'md' => 5,
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->prefix(fn(Forms\Get $get)=>$get('product.unit.name'))
                            ->numeric($decimalPlaces = 2)
                            ->default(1)
                            ->gt(0)
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required()
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        $product = Product::find($data['product_id']);
                        $price = floatVal($product?->price);
                        $qty = floatVal($data['qty']);
                        $data['price'] = $price;
                        $data['total'] = $price * $qty;
                        $data['consuming_qty'] = $qty*floatVal($product->uoc_qty??0);
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
                ->required(),
            Forms\Components\Select::make('purchase_order_id')
                ->relationship('purchase_order', 'reference_no')
                ->searchable()
                ->disabled(fn (Component $livewire) => $livewire?->purchaseOrder?->id != null),
            Forms\Components\Select::make('store_id')
                ->relationship('store', 'name')
                ->required()
                ->live()
                ->disabled(fn (Component $livewire) => $livewire?->purchaseOrder?->id != null),
            Forms\Components\Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->searchable()
                ->required()
                ->disabled(fn (Component $livewire) => $livewire?->purchaseOrder?->id != null)
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('gst_tin_no'),
                ])
                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                    return $action
                        ->modalHeading('Create Supplier')
                        ->modalWidth('lg');
                }),
            Forms\Components\DatePicker::make('receipt_date')
                ->format('Y-m-d')
                ->native(false)
                ->default(now())
                ->required(),
        ];
    }


    public static function shouldRegisterNavigation(): bool
    {
        return true;
        return auth()->user()->role == 'admin';
    }
}
