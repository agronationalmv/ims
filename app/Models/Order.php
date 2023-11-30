<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded=[];

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
        return "CN".str_pad(strval($nextId),6,"0",STR_PAD_LEFT);
    }

}
