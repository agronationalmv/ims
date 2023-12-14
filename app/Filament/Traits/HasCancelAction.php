<?php

namespace App\Filament\Traits;

use Filament\Actions;

trait HasCancelAction{
    public function getCancelFormAction(){
        return Actions\Action::make('back')
                    ->label(__('Back'))
                    ->url($this->previousUrl ?? static::getResource()::getUrl())
                    ->color('gray');
    }
}