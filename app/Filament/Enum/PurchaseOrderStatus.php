<?php

namespace App\Filament\Enum;

use App\Filament\Concerns\HasActions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseOrderStatus: string implements HasColor, HasLabel, HasActions
{
    case Draft = 'draft';
    case Reviewing = 'reviewing';
    case Published = 'published';
    case Rejected = 'rejected';
    
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Reviewing => 'Reviewing',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Reviewing => 'warning',
            self::Published => 'success',
            self::Rejected => 'danger',
        };
    }

    public function getActions(): ?array
    {
        return match ($this) {
            self::Draft => ['publish'=>self::Published,'reject'=>self::Rejected],
            // self::Reviewing => ['publish'=>self::Published,'reject'=>self::Rejected],
            self::Published => [],
            self::Rejected => [],
        };
    }
}