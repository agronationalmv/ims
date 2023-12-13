<?php

use App\Models\Product;
use App\Models\Store;
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
        Schema::create('product_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)
                        ->constrained()
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
            $table->foreignIdFor(Store::class)
                        ->constrained()
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
            $table->decimal('min_qty',8,3)->default(0);
            $table->decimal('init_qty',8,3)->default(0);
            $table->decimal('qty',8,3)->default(0);
            $table->decimal('price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stores');
    }
};
