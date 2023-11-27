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
    protected static ?string $model = PoReceive::class;

    protected ?PurchaseOrder $purchaseOrder;

    protected static ?string $modelLabel = 'Item Receive';
    protected static ?string $pluralModelLabel = 'Item Receive';

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
                            ->content(fn (PurchaseOrder $record): ?string => $record->updated_at),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created On')
                            ->content(fn (PurchaseOrder $record): ?string => $record->created_at)

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?string $operation) => $operation!='create'),
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
                Tables\Columns\TextColumn::make('received_date')
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
                        ->columnSpan([
                            'md' => 5,
                        ])
                        ->searchable(),

                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->live(debounce: 500)
                        ->numeric($decimalPlaces=2)
                        ->default(1)
                        ->columnSpan([
                            'md' => 2,
                        ])
                        ->required(),
                ])
                ->defaultItems(1)
                ->columns([
                    'md' => 10,
                ])
                ->required()
            ];
        }

        return [
            // Forms\Components\Placeholder::make('PO #')
            //     ->content(fn(Component $livewire)=>$livewire->purchaseOrder?->reference_no)
            //     ->columnSpan(2),
            Forms\Components\Select::make('purchase_order_id')
                ->label('PO#')
                ->live()
                ->afterStateUpdated(fn(Component $livewire, ?string $state)=>$livewire->purchaseOrder=PurchaseOrder::find($state))
                ->options(PurchaseOrder::query()->where('status',PurchaseOrderStatus::Approved)->pluck('reference_no', 'id'))
                ->required(),
            Forms\Components\TextInput::make('reference_no')
                ->maxLength(255),
            Forms\Components\DatePicker::make('received_date')
                ->required(),
        ]; 
    }

    
}
