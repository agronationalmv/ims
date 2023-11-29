<?php

use App\Models\PurchaseOrder;
use App\Models\Supplier;
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
        Schema::create('po_receives', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseOrder::class)->nullable();
            $table->string('reference_no')->nullable();
            $table->date('receipt_date');
            $table->foreignIdFor(Supplier::class)->nullable();
            $table->decimal('subtotal')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('total_gst')->default(0);
            $table->decimal('net_total')->default(0);
            $table->foreignId('received_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_receives');
    }
};
