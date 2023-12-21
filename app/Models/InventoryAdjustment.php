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

    public function store() : BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function items() : HasMany {
        return $this->hasMany(AdjustmentDetail::class);
    }

    protected static function boot(){
        parent::boot();
        self::creating(function($model){
            $model->reference_no=self::generateReference();
        });
    }

    protected static function generateReference(){
        $nextId=self::count()+1;
        return "IA".str_pad(strval($nextId),6,"0",STR_PAD_LEFT);
    }
}
