<?php

namespace App\Models;

use App\Models\Contracts\TransactionableModel;
use App\Models\Enums\TransactionTypeEnum;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class AdjustmentDetail extends TransactionableModel
{
    use HasFactory;

    protected $guarded=[];

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function inventory_adjustment() : BelongsTo {
        return $this->belongsTo(InventoryAdjustment::class,'inventory_adjustment_id');
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

    protected static function boot(){
        parent::boot();
        self::created(function($model){
            app(ProductService::class)->updateProductBalance($model->inventory_adjustment->store_id,$model->product);
        });
    }

}
