<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Unit::class);
            $table->foreignIdFor(Unit::class,'uoc_id')->nullable();
            $table->decimal('uoc_qty',8,3)->default(1);
            $table->decimal('qty',8,3)->default(0);
            $table->decimal('price')->default(0);
            $table->decimal('price_each')->default(0);
            $table->decimal('gst_rate',6,3)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
