<?php

namespace App\Models;

use App\Models\Contracts\TransactionableModel;
use App\Models\Enums\TransactionTypeEnum;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends TransactionableModel
{
    use HasFactory;

    protected $guarded=[];

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function order() : BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function getTransactionType()
    {
        return TransactionTypeEnum::Out->value;
    }

    protected static function boot(){
        parent::boot();
        self::created(function($model){
            app(ProductService::class)->updateProductBalance($model->order->store_id,$model->product);
        });
    }
}
