<?php

namespace App\Models;

use App\Jobs\ProductBalanceUpdateJob;
use App\Models\Contracts\HasInventory;
use App\Models\Contracts\TransactionableModel;
use App\Models\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function adjustment_type() : BelongsTo {
        return $this->belongsTo(AdjustmentType::class);
    }

    public function items() : HasMany {
        return $this->hasMany(AdjustmentDetail::class);
    }
}
