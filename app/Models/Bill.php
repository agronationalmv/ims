<?php

namespace App\Models;

use App\Filament\Enum\BillStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'status'=>BillStatus::class
    ];

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }
    
    public function items() : HasMany {
        return $this->hasMany(BillDetail::class);
    }

}
