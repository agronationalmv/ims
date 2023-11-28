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
    protected static ?int $navigationSort = 4;

    protected static ?string $model = PoReceive::class;

    protected ?PurchaseOrder $purchaseOrder;

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

                        Forms\Components\Section::make('Items')
                            ->schema(static::getFormSchema('items')),
                    ])
                    ->columnSpan(['lg' => fn (?string $operation) => $operation=='view'?2:3]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('subtotal')
                            ->label('Subtotal')
                            ->content(fn (PurchaseOrder $record): ?string => $record->subtotal),

                        Forms\Components\Placeholder::make('total_gst')
                            ->label('GST')
                            ->content(fn (PurchaseOrder $record): ?string => $record->total_gst),

                        Forms\Components\Placeholder::make('net_total')
                            ->label('Total')
                            ->content(fn (PurchaseOrder $record): ?string => $record->net_total)

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?string $operation) => $operation!='view'),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                ->searchable(),
                Tables\Columns\TextColumn::make('purchase_order.reference_no')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('received_by.name')
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
        if($section=='items'){
            return [
                Forms\Components\Repeater::make('items')
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Product')
                        ->options(Product::query()->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function($state, Forms\Set $set){
                            $product=Product::find($state);
                            $set('price', $product?->price ?? 0);
                            $set('gst_rate', $product?->gst_rate ?? 0);
                            $set('total', ($product?->price*$product?->qty*(1+$product?->gst_rate)) ?? 0);
                        })
                        ->columnSpan([
                            'md' => 5,
                        ])
                        ->searchable(),

                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                            $set('total', round(($get('price')*$get('qty')*(1+$get('gst_rate'))) ?? 0,2));
                        })
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
                ->required()
            ];
        }

        return [
            Forms\Components\TextInput::make('reference_no')
                ->required(),
            Forms\Components\Select::make('purchase_order_id')
                ->relationship('purchase_order', 'name')
                ->searchable()
                ->required(),
            Forms\Components\DatePicker::make('receipt_date')
                ->required(),
        ]; 
    }

    
}
