<?php

use App\Models\Bill;
use App\Models\Product;
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
        Schema::create('bill_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bill::class);
            $table->foreignIdFor(Product::class);
            $table->decimal('qty',8,3);
            $table->decimal('consuming_qty',8,3)->default(0);
            $table->decimal('price');
            $table->decimal('gst_rate',6,3)->default(0);
            $table->decimal('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_details');
    }
};
