<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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

    protected static ?string $modelLabel = 'Consumption';
    protected static ?string $pluralModelLabel = 'Consumptions';

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
                    ->columnSpan(['lg' => fn (?string $operation) => $operation=='create'?3:2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Updated On')
                            ->content(fn (?Order $record): ?string => $record?->updated_at),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created On')
                            ->content(fn (?Order $record): ?string => $record?->created_at)

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?string $operation) => $operation=='create'),
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
                ->live()
                ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                    self::updateTotals($get,$set);
                })
                ->required()
            ];
        }

        return [
            Forms\Components\TextInput::make('reference_no')
                ->maxLength(255),
            Forms\Components\DatePicker::make('order_date')
                ->required(),
            Forms\Components\Select::make('requested_by_id')
                ->label('Requested By')
                ->options(User::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
        ]; 
    }
}
