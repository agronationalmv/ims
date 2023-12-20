<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use App\Tables\Columns\ReportColumn;
use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Filament\Forms;
use Filament\Tables\Filters\Filter;

class ReportWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?Model $report=null;

    protected function getTableHeading(): string | Htmlable | null
    {
        return $this->report?->title;
    }

    protected function getTableQuery(): Builder
    {

        return $this->report->provider::query();

    }

    protected function getTableColumns(): array
    {
        $columns=[];
        foreach($this->report->provider::getColumns() as $label=>$key){
            $columns[]=ReportColumn::make($key)
                            ->label($label);
        }
        return $columns;
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        return $query;
    }

    public function getTableRecordKey($record): string{
        return 'id';
    }

    public function table(Table $table): Table
    {
        return $table
                ->query($this->getTableQuery())
                ->columns($this->getTableColumns())
                ->filters([
                    Filter::make('filters')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                ])
                ->headerActions([
                    Action::make('export')
                                ->action(fn (Component $livewire) => $livewire->report->provider->export($livewire->tableFilters['filters']??[]))
                ]);
    }

}
