<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?int $navigationSort = 10;

    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-down-on-square-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('report')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('description')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('download')
                    ->label('Download Report')
                    ->action(function ($record, array $data) {
                        // Use the provider attribute to get the report class instance
                        $reportInstance = $record->provider;
                        
                        // Ensure filters are decoded properly
                        $filters = json_decode($data['filters'], true);

                        // Check if export method exists and call it
                        if (method_exists($reportInstance, 'export')) {
                            return $reportInstance->export($filters);
                        } else {
                            throw new \Exception('Export method not found on the report class.');
                        }
                    })
                    ->form([
                        Forms\Components\Select::make('format')
                            ->options([
                                'pdf' => 'PDF',
                                'csv' => 'CSV',
                                'xlsx' => 'Excel',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('filters')
                            ->label('Filters (JSON)')
                            ->default('{}')
                            ->required(),
                    ])
                    ->icon('heroicon-c-arrow-down-on-square-stack')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ReportView::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
