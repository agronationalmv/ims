<?php

namespace App\Models;

use App\Filament\Enum\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'status'=>PurchaseOrderStatus::class
    ];

    public function expense_account() : BelongsTo {
        return $this->belongsTo(ExpenseAccount::class);
    }

    public function purchase_request() : BelongsTo {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function store() : BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function items() : HasMany {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function po_receives() : HasMany {
        return $this->hasMany(PoReceive::class);
    }
}
