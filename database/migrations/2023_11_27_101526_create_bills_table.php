<?php

use App\Filament\Enum\BillStatus;
use App\Models\ExpenseAccount;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable();
            $table->foreignId('receipt_id')->nullable();
            $table->foreignIdFor(ExpenseAccount::class)->nullable();
            $table->foreignIdFor(PurchaseOrder::class)->nullable();
            $table->string('bill_date')->nullable();
            $table->foreignIdFor(Supplier::class)->nullable();
            $table->decimal('subtotal')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('total_gst')->default(0);
            $table->decimal('net_total')->default(0);
            $table->string('status')->default(BillStatus::Unpaid);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
