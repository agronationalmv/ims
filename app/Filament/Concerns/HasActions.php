<?php

namespace App\Filament\Concerns;

interface HasActions{
    public function getActions():array|null;
}