<?php

namespace App\Filament\Resources;

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Resources\PoReceiveResource\Pages;
use App\Filament\Resources\PoReceiveResource\RelationManagers;
use App\Models\PoReceive;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PoReceiveDetail;
use App\Models\ProductStore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;
use Closure;

class PoReceiveResource extends Resource
{
    protected static ?string $navigationGroup = "Manage Inventory";
    protected static ?int $navigationSort = 2;
    protected static ?string $model = PoReceive::class;
    protected ?PurchaseOrder $purchaseOrder = null;
    protected static ?string $modelLabel = 'Item Receipt';
    protected static ?string $pluralModelLabel = 'Item Receipts';
    protected static ?string $navigationIcon = 'heroicon-s-tag';

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
                            ->options(function (Forms\Get $get) {
                                return Product::query()->whereHas('stores', function ($q) use ($get) {
                                    $q->where('store_id', $get('../../store_id'));
                                })->pluck('name', 'id');
                            })
                            ->required()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $product = Product::find($get('product_id'));
                                $set('product', $product);
                                $set('gst_rate', $product->gst_rate);

                                $purchaseOrderId = $get('../../purchase_order_id');
                                $productId = $get('product_id');
                                $currentAllocatedQty = PoReceiveDetail::whereHas('po_receive', function ($query) use ($purchaseOrderId) {
                                    $query->where('purchase_order_id', $purchaseOrderId);
                                })
                                ->where('product_id', $productId)
                                ->sum('qty') ?? 0;

                                $maxQty = PurchaseOrderDetail::where('purchase_order_id', $purchaseOrderId)
                                    ->where('product_id', $productId)
                                    ->value('qty') ?? 0;

                                $remaining = $maxQty - $currentAllocatedQty;
                                $set('qty_suffix', "Remaining: {$remaining}");
                            })
                            ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                                if ($get('product_id')) {
                                    $product = Product::find($get('product_id'));
                                    $set('product', $product);
                                    $set('gst_rate', $product->gst_rate);

                                    $purchaseOrderId = $get('../../purchase_order_id');
                                    $productId = $get('product_id');
                                    $currentAllocatedQty = PoReceiveDetail::whereHas('po_receive', function ($query) use ($purchaseOrderId) {
                                        $query->where('purchase_order_id', $purchaseOrderId);
                                    })
                                    ->where('product_id', $productId)
                                    ->sum('qty') ?? 0;

                                    $maxQty = PurchaseOrderDetail::where('purchase_order_id', $purchaseOrderId)
                                        ->where('product_id', $productId)
                                        ->value('qty') ?? 0;

                                    $remaining = $maxQty - $currentAllocatedQty;
                                    $set('qty_suffix', "Remaining: {$remaining}");
                                }
                            })
                            ->live()
                            ->columnSpan([
                                'md' => 5,
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->prefix(fn(Forms\Get $get) => $get('product.unit.name'))
                            ->suffix(fn(Forms\Get $get) => $get('qty_suffix'))
                            ->numeric($decimalPlaces = 2)
                            ->default(1)
                            ->gt(0)
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required()
                            ->rules([
                                fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    $purchaseOrderId = $get('../../purchase_order_id');
                                    $productId = $get('product_id');

                                    // Check the current allocated quantity and total quantity for the PO
                                    $currentAllocatedQty = PoReceiveDetail::whereHas('po_receive', function ($query) use ($purchaseOrderId) {
                                        $query->where('purchase_order_id', $purchaseOrderId);
                                    })
                                    ->where('product_id', $productId)
                                    ->sum('qty') ?? 0;

                                    if (empty($currentAllocatedQty)) {
                                        $currentAllocatedQty = 0;
                                    }

                                    $maxQty = PurchaseOrderDetail::where('purchase_order_id', $purchaseOrderId)
                                        ->where('product_id', $productId)
                                        ->value('qty') ?? 0;

                                    $remaining = $maxQty - $currentAllocatedQty;

                                    if (($currentAllocatedQty + $value) > $maxQty) {
                                        $fail("The quantity exceeds the maximum limit for the purchase order. Maximum: {$maxQty}(Remaining: {$remaining}.000)");
                                    }

                                    if ($value == 0 || $value < 0) {
                                        $fail("The quantity Cannot be 0");
                                    }
                                },
                            ])
                            ->reactive(),

                        Forms\Components\Hidden::make('gst_rate'),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        $product = Product::find($data['product_id']);
                        $price = floatval($product?->price);
                        $qty = floatval($data['qty']);
                        $data['price'] = $price;
                        $data['total'] = $price * $qty * (1 + $data['gst_rate']);
                        $data['consuming_qty'] = $qty * floatval($product->uoc_qty ?? 0);
                        $data['gst_rate'] = $product->gst_rate;
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

            Forms\Components\FileUpload::make('attachment')
                ->label('Invoice & Delivery Note')
                ->disk('public')
                ->required()
                ->downloadable()
                ->openable()
                ->preserveFilenames()
                ->required(),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
        return auth()->user()->role == 'admin';
    }
}
