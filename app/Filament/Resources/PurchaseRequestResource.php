<?php

namespace App\Filament\Resources;

use App\Filament\Enum\PurchaseRequestStatus;
use App\Filament\Resources\PurchaseRequestResource\Pages;
use App\Filament\Resources\PurchaseRequestResource\RelationManagers;
use App\Models\Product;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PurchaseRequestResource extends Resource
{
    protected static ?int $navigationSort = 3;

    protected static ?string $model = PurchaseRequest::class;

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
                        ->schema(static::getItemSchema()),
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
                        ->content(function(?PurchaseRequest $record){
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
                Tables\Columns\TextColumn::make('requested_by.name'),
                Tables\Columns\TextColumn::make('store.name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(PurchaseRequestStatus::class),
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
            'index' => Pages\ListPurchaseRequests::route('/'),
            'create' => Pages\CreatePurchaseRequest::route('/create'),
            'view' => Pages\ViewPurchaseRequest::route('/{record}'),
            'edit' => Pages\EditPurchaseRequest::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(){
        return [
            Forms\Components\TextInput::make('reference_no')
                ->readOnly()
                ->visible(fn($operation)=>$operation=='view'),
            Forms\Components\Select::make('store_id')
                ->relationship('store', 'name')
                ->required()
                ->live(),
            Forms\Components\Select::make('expense_account_id')
                ->relationship('expense_account', 'name')
                ->required(),
            Forms\Components\Select::make('department_id')
                ->relationship('department', 'name')
                ->required(),
            Forms\Components\Textarea::make('purpose')

        ];
    }

    public static function getItemSchema(){
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
                    ->required(fn(Forms\Get $get)=>empty($get('description')))
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
                Forms\Components\Textarea::make('description')
                    ->required(fn(Forms\Get $get)=>empty($get('product_id')))
                    ->columnSpan([
                        'md' => 5,
                    ]),
                Forms\Components\TextInput::make('qty')
                    ->label('Quantity')
                    ->prefix(fn(Forms\Get $get)=>$get('product.unit.name'))
                    ->live(debounce: 500)
                    ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                        $set('total', round(($get('price')*$get('qty')*(1+$get('gst_rate'))) ?? 0,2));
                    })
                    ->gt(0)
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
                    ->default(0)
                    ->columnSpan([
                        'md' => 3,
                    ]),
                Forms\Components\TextInput::make('gst_rate')
                    ->label('GST Rate')
                    ->disabled()
                    ->dehydrated()
                    ->numeric($decimalPlaces=3)
                    ->default(0)
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
            ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                self::updateTotals($get,$set);
            })
            ->required()
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
