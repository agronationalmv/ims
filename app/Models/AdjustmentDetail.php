<?php

namespace App\Models;

use App\Models\Contracts\TransactionableModel;
use App\Models\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdjustmentDetail extends TransactionableModel
{
    use HasFactory;

    protected $guarded=[];

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function getTransactionType()
    {
        return TransactionTypeEnum::Out->value;
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function adjustment_type() : BelongsTo {
        return $this->belongsTo(AdjustmentType::class);
    }

}
