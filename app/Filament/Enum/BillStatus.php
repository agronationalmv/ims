<?php

namespace App\Filament\Enum;

use App\Filament\Concerns\HasActions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillStatus: string implements HasColor, HasLabel, HasActions
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Void = 'void';
    
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::Paid => 'Paid',
            self::Void => 'Void',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Unpaid => 'gray',
            self::Paid => 'success',
            self::Void => 'danger',
        };
    }

    public function getActions(): ?array
    {
        return match ($this) {
            self::Unpaid => ['pay'=>self::Paid,'void'=>self::Void],
            self::Paid => [],
            self::Void => [],
        };
    }
}