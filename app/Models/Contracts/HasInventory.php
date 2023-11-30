<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasInventory{
    public function items():HasMany;
}