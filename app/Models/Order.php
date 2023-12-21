<?php

namespace App\Models;

use App\Jobs\ProductBalanceUpdateJob;
use App\Models\Contracts\HasInventory;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Order extends Model implements HasInventory
{
    use HasFactory;

    protected $guarded=[];

    public function store() : BelongsTo {
        return $this->belongsTo(Store::class);
    }
    
    public function items() : HasMany {
        return $this->hasMany(OrderDetail::class);
    }

    public function requested_by() : BelongsTo {
        return $this->belongsTo(User::class,'requested_by_id');
    }

    protected static function boot(){
        parent::boot();
        self::creating(function($model){
            $model->reference_no=self::generateReference();
        });
    }

    protected static function generateReference(){
        $nextId=self::count()+1;
        return "SI".str_pad(strval($nextId),6,"0",STR_PAD_LEFT);
    }

}
