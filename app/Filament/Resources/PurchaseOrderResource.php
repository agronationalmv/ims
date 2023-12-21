<?php

namespace App\Filament\Resources;

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use Closure;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class PurchaseOrderResource extends Resource
{
    protected static ?int $navigationSort = 4;

    protected static ?string $model = PurchaseOrder::class;

    public ?PurchaseRequest $purchaseRequest=null;


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
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_gst')
                            ->label('Total GST')
                            ->disabled(),
                        Forms\Components\TextInput::make('net_total')
                            ->label('Total')
                            ->disabled(),
                        Forms\Components\Placeholder::make('status')
                            ->content(function(?PurchaseOrder $record){
                                        return new HtmlString(
                                            Blade::render('<x-filament::badge color="{{$record->status->getColor()}}">
                                                    {{$record->status}}
                                                </x-filament::badge>',['record'=>$record])
                                            );
                            })
                            ->hidden(fn (?string $operation) => $operation=='create'),

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
                Tables\Columns\TextColumn::make('supplier.name'),
                Tables\Columns\TextColumn::make('net_total')
                    ->numeric($decimalPlaces=2)
                    ->prefix('MVR '),
                Tables\Columns\TextColumn::make('status')
                    ->badge(PurchaseOrderStatus::class),
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
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\PoReceivesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create/{purchaseRequest?}'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
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
                        ->required()
                        ->live()
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $product=Product::find($get('product_id'));
                            $price=floatVal($product?->price);
                            $gst_rate=floatVal($product?->gst_rate);
                            $set('price', $price);
                            $set('gst_rate', $gst_rate);
                            $total=$price*(1+$gst_rate);
                            $set('total', $total);
                            $product?->unit;
                            $set('product',$product);
                        })
                        ->columnSpan([
                            'md' => 5,
                        ])
                        ->searchable(),

                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->prefix(fn(Forms\Get $get)=>$get('product.unit.name'))
                        ->live(debounce: 500)
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $set('total', round(($get('price')*$get('qty')*(1+$get('gst_rate'))) ?? 0,2));
                        })
                        ->rules([
                            fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                $purchase_request_id=$get('../../purchase_request_id');
                                if($purchase_request_id){
                                    $prItem=PurchaseRequestDetail::where('purchase_request_id',$purchase_request_id)
                                        ->where('product_id',$get('product_id'))
                                        ->first();

                                    if($prItem){
                                        if($value>floatval($prItem->balance)){
                                            $fail("The quantity must be less than or equal to {$prItem->balance}");
                                        }
                                    }else{
                                        $fail("Invalid Product");
                                    }


                                }

                                if($get('qty')<=0){
                                    $fail("The quantity must be greater than 0");
                                }


                            },
                        ])
                        ->numeric($decimalPlaces=2)
                        ->default(1)
                        ->columnSpan([
                            'md' => 2,
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $set('total', round(($get('price')*$get('qty')*(1+$get('gst_rate'))) ?? 0,2));
                        })
                        ->dehydrated()
                        ->numeric($decimalPlaces=2)
                        ->required()
                        ->columnSpan([
                            'md' => 3,
                        ]),
                    Forms\Components\TextInput::make('gst_rate')
                        ->label('GST Rate')
                        ->disabled()
                        ->dehydrated()
                        ->numeric($decimalPlaces=3)
                        ->required()
                        ->columnSpan([
                            'md' => 2,
                        ]),
                    Forms\Components\TextInput::make('total')
                        ->label('Subtotal')
                        ->disabled()
                        ->dehydrated()
                        ->numeric($decimalPlaces=2)
                        ->required()
                        ->columnSpan([
                            'md' => 2,
                        ]),
                ])
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $data['total']=$data['price']*$data['qty']*(1+$data['gst_rate']);
                    return $data;
                })
                ->defaultItems(1)
                ->columns([
                    'md' => 10,
                ])
                ->live()
                ->afterStateHydrated(function(Forms\Get $get, Forms\Set $set){
                    self::updateTotals($get,$set);
                })
                ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                    self::updateTotals($get,$set);
                })
                ->addable(fn(Forms\Get $get)=>$get('purchase_request_id')==null)
                ->required()
            ];
        }

        return [
            Forms\Components\TextInput::make('reference_no')
                ->required(),
            Forms\Components\DatePicker::make('purchase_order_date')
                ->format('Y-m-d')
                ->native(false)
                ->default(now())
                ->required(),
            Forms\Components\Select::make('store_id')
                ->relationship('store', 'name')
                ->disabled(fn(Forms\Get $get)=>$get('purchase_request_id'))
                ->required()
                ->live(),
            Forms\Components\Select::make('expense_account_id')
                ->relationship('expense_account', 'name')
                ->disabled(fn(Forms\Get $get)=>$get('purchase_request_id'))
                ->required(),
            Forms\Components\Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->searchable()
                ->required()
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
            Forms\Components\Placeholder::make('Purchase Request')
                        ->content(fn(Forms\Get $get)=>$get('purchase_request_reference_no')),
        ]; 
    }

    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('items'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['qty']));
    

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['price'] * $product['qty']);
        }, 0);

        $total_gst = $selectedProducts->reduce(function ($gst_total, $product) {
            return $gst_total + ($product['price']*$product['gst_rate'] * $product['qty']);
        }, 0);
    
        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_gst', number_format($total_gst, 2, '.', ''));
        $set('net_total', number_format($subtotal + $total_gst, 2, '.', ''));
    }
    
}
