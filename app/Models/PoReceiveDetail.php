<?php

namespace App\Models;

use App\Models\Contracts\TransactionableModel;
use App\Models\Enums\TransactionTypeEnum;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoReceiveDetail extends TransactionableModel
{
    use HasFactory;

    protected $guarded=[];

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function po_receive() : BelongsTo {
        return $this->belongsTo(PoReceive::class);
    }

    public function getTransactionType()
    {
        return TransactionTypeEnum::In->value;
    }

    protected static function boot(){
        parent::boot();
        self::created(function($model){
            app(ProductService::class)->updateProductBalance($model->po_receive->store_id,$model->product);
        });
    }

}
