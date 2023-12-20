<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Filament\Resources\ReportResource\Widgets\ReportWidget;
use App\Filament\Widgets\DailyConsumptionWidget;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ReportView extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.resources.report-resource.pages.report-view';

    use InteractsWithRecord;
    
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
 
        static::authorizeResourceAccess();
    }

    public function getHeaderWidgets(): array
    {
        return [
            ReportWidget::make([
                'report'=>$this->record,
            ]),
        ];
    }
}
