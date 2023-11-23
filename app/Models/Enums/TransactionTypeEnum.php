<?php

namespace App\Models\Enums;

enum TransactionTypeEnum: string{
    case In = 'in';
    case Out = 'out';
}