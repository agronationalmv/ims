<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoReceive extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function purchase_order() : BelongsTo {
        return  $this->belongsTo(PurchaseOrder::class);
    }

    public function items() : HasMany {
        return $this->hasMany(PoReceiveDetail::class);
    }

    public function received_by() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
