<?php

namespace App\Filament\Enum;

use App\Filament\Concerns\HasActions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseRequestStatus: string implements HasColor, HasLabel, HasActions
{
    case Draft = 'draft';
    case Pending = 'Pending';
    case Approved = 'approved';
    case Completed = 'completed';
    case Rejected = 'rejected';
    
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Approved => 'Finalized',
            self::Completed => 'Closed',
            self::Rejected => 'Rejected',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Pending => 'gray',
            self::Approved => 'warning',
            self::Completed => 'success',
            self::Rejected => 'danger',
        };
    }

    public function getActions(): ?array
    {
        return match ($this) {
            self::Draft => ['submit'=>self::Pending,'reject'=>self::Rejected],
            self::Pending => ['Finalize'=>self::Approved,'Cancel'=>self::Rejected],
            self::Approved => ['Close'=>self::Completed,'Cancel'=>self::Rejected],
            self::Completed => [],
            self::Rejected => [],
        };
    }
}