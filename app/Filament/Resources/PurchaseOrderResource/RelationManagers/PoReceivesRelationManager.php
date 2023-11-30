<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Resources\PoReceiveResource;
use App\Models\PoReceive;
use App\Models\Product;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PoReceivesRelationManager extends RelationManager
{
    protected static string $relationship = 'po_receives';

    protected static ?string $title = "Receipts";

    protected static ?string $modelLabel = "Receipt";
    protected static ?string $pluralModelLabel = "Receipts";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getMainFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make()
                            ->schema(static::getMainFormSchema('items')),
                    ])
                    ->columnSpan(['lg' => fn (?string $operation) => $operation == 'view' ? 2 : 3]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('subtotal')
                            ->label('Subtotal')
                            ->content(fn (?PoReceive $record): ?string => $record->subtotal),

                        Forms\Components\Placeholder::make('total_gst')
                            ->label('GST')
                            ->content(fn (?PoReceive $record): ?string => $record->total_gst),

                        Forms\Components\Placeholder::make('net_total')
                            ->label('Total')
                            ->content(fn (?PoReceive $record): ?string => $record->net_total)

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?string $operation) => $operation != 'view'),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('New Receipt')
                    ->url(fn (): string => route('filament.admin.resources.po-receives.create',['purchaseOrder'=>$this->ownerRecord->id]))
                    ->visible(function(RelationManager $livewire){
                        return $livewire->ownerRecord->status==PurchaseOrderStatus::Approved;
                    })
                    // ->mutateFormDataUsing(function (array $data): array {
                    //     $po = $this->ownerRecord;
                    //     $data['received_by_id'] = auth()->id();
                    //     $data['supplier_id'] = $po->supplier_id;
                    //     return $data;
                    // })
                    // ->after(function(){
                    //     $balance=$this->ownerRecord
                    //                 ->items->reduce(function($carry,$item){
                    //                     return $carry+$item->balance;
                    //                 },0);
                    //     if($balance<=0){
                    //         $this->ownerRecord->status = PurchaseOrderStatus::Completed;
                    //         $this->ownerRecord->save();
                    //     }
                    // }),
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

    public static function getMainFormSchema(string $section = null): array
    {
        if ($section == 'items') {
            return [
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(Product::query()->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $product = Product::find($state);
                                $set('price', $product?->price ?? 0);
                                $set('gst_rate', $product?->gst_rate ?? 0);
                                $set('total', ($product?->price * $product?->qty * (1 + $product?->gst_rate)) ?? 0);
                            })
                            ->columnSpan([
                                'md' => 5,
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $set('total', round(($get('price') * $get('qty') * (1 + $get('gst_rate'))) ?? 0, 2));
                            })
                            ->numeric($decimalPlaces = 2)
                            ->default(1)
                            ->rules([
                                fn (Forms\Get $get, RelationManager $livewire): Closure => function (string $attribute, $value, Closure $fail) use ($get,$livewire) {
                                    $product_id=$get('product_id');
                                    if($product_id){
                                        $item=$livewire->ownerRecord->items()->where('product_id',$product_id)->first();
                                        if($item->balance<$value){
                                            $fail("The quantity must be less than ".strval($item->balance));
                                        }
                                    }
                                    if($value<=0){
                                        $fail("The quantity must be greater than 0");
                                    }

                                },
                            ])

                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $set('total', round(($get('price') * $get('qty') * (1 + $get('gst_rate'))) ?? 0, 2));
                            })
                            ->dehydrated()
                            ->numeric($decimalPlaces = 2)
                            ->required()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Forms\Components\TextInput::make('gst_rate')
                            ->label('GST Rate')
                            ->disabled()
                            ->dehydrated()
                            ->numeric($decimalPlaces = 3)
                            ->required()
                            ->columnSpan([
                                'md' => 2,
                            ]),
                        Forms\Components\TextInput::make('total')
                            ->label('Subtotal')
                            ->disabled()
                            ->dehydrated()
                            ->numeric($decimalPlaces = 2)
                            ->required()
                            ->columnSpan([
                                'md' => 2,
                            ]),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        $data['total'] = $data['price'] * $data['qty'] * (1 + $data['gst_rate']);
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
            Forms\Components\DatePicker::make('receipt_date')
                ->required(),
        ];
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
