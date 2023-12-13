<?php

namespace App\Models;

use App\Jobs\ProductBalanceUpdateJob;
use App\Models\Contracts\HasInventory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoReceive extends Model implements HasInventory
{
    use HasFactory;

    protected $guarded=[];

    public function purchase_order() : BelongsTo {
        return  $this->belongsTo(PurchaseOrder::class);
    }

    public function store() : BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function items() : HasMany {
        return $this->hasMany(PoReceiveDetail::class);
    }

    public function received_by() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }
}
