<?php

namespace App\Models;

use App\Filament\Enum\PurchaseRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'status'=>PurchaseRequestStatus::class
    ];

    public function expense_account() : BelongsTo {
        return $this->belongsTo(ExpenseAccount::class);
    }

    public function requested_by() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function purchase_orders() : HasMany {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function department() : BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function store() : BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function items() : HasMany {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    protected static function boot(){
        parent::boot();
        self::creating(function($model){
            $model->reference_no=self::generateReference();
        });
    }

    protected static function generateReference(){
        $nextId=self::count()+1;
        return "PR".str_pad(strval($nextId),6,"0",STR_PAD_LEFT);
    }

}
