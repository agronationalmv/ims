<?php

namespace App\Filament\Resources;

use App\Filament\Enum\BillStatus;
use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Models\Bill;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class BillResource extends Resource
{
    protected static ?int $navigationSort = 6;

    protected static ?string $model = Bill::class;

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
                            ->label('GST')
                            ->disabled(),
                        Forms\Components\TextInput::make('net_total')
                            ->label('Total')
                            ->disabled()
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create/{receipt}'),
            'view' => Pages\ViewBill::route('/{record}'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
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
                        ->relationship('product','name')
                        ->live(debounce: 500)
                        ->required()
                        ->dehydrated()
                        ->disabled()
                        ->columnSpan([
                            'md' => 5,
                        ]),
                    Forms\Components\Hidden::make('product_id'),
                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->prefix(fn(Forms\Get $get)=>$get('product.unit.name'))
                        ->live(debounce: 500)
                        ->numeric($decimalPlaces=2)
                        ->disabled(true)
                        ->dehydrated()
                        ->gt(0)
                        ->columnSpan([
                            'md' => 2,
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->live(debounce: 500)
                        ->dehydrated()
                        ->numeric($decimalPlaces=2)
                        ->required()
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $set('total', round(($get('price')*$get('qty')*(1+$get('gst_rate'))) ?? 0,2));
                        })
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
                    $product = Product::find($data['product_id']);
                    $price = floatVal($data['price']);
                    $qty = floatVal($data['qty']);
                    $gst = floatVal($data['gst_rate']);
                    $data['total'] = $price * $qty*(1+$gst);
                    $data['consuming_qty'] = $qty*floatVal($product->uoc_qty??0);
                    return $data;
                })
                ->defaultItems(1)
                ->columns([
                    'md' => 10,
                ])
                ->deletable(false)
                ->addable(false)
                ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                    self::updateTotals($get,$set);
                })
                ->required()
            ];
        }

        return [
            Forms\Components\TextInput::make('reference_no')
                ->required(),
            Forms\Components\Select::make('expense_account_id')
                ->relationship('expense_account', 'name')
                ->disabled(fn(Forms\Get $get)=>$get('purchase_request_id'))
                ->required(),
            Forms\Components\DatePicker::make('bill_date')
                ->format('Y-m-d')
                ->native(false)
                ->default(now())
                ->required(),
            Forms\Components\Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->disabled()
                ->required(),
            Forms\Components\Placeholder::make('Purchase Order')
                ->content(fn(Component $livewire)=>$livewire->record->purchase_order?->reference_no)
                ->disabled()
                ->visible(fn(string $operation)=>$operation=='view'),
            Forms\Components\FileUpload::make('attachment')
                ->disk('public')
                ->required()
                ->downloadable()
                ->openable()
                ->preserveFilenames()



        ]; 
    }

    // This function updates totals based on the selected products and quantities
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
