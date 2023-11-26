<?php

namespace App\Filament\Traits;

use Filament\Actions;

trait HasCancelAction{
    public function getCancelFormAction(){
        return Actions\Action::make('cancel')
                    ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                    ->url($this->previousUrl ?? static::getResource()::getUrl())
                    ->color('gray');
    }
}