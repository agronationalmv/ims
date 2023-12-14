<?php

use App\Models\Product;
use App\Models\PurchaseRequest;
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
        Schema::create('purchase_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseRequest::class);
            $table->foreignIdFor(Product::class)->nullable();
            $table->mediumText('description')->nullable();
            $table->decimal('qty',8,3);
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
        Schema::dropIfExists('purchase_request_details');
    }
};
