<?php

use App\Filament\Enum\PurchaseOrderStatus;
use App\Models\Department;
use App\Models\ExpenseAccount;
use App\Models\PurchaseRequest;
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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable();
            $table->foreignIdFor(Supplier::class)->nullable();
            $table->foreignIdFor(ExpenseAccount::class)->nullable();
            $table->date('purchase_order_date')->nullable();
            $table->foreignIdFor(PurchaseRequest::class)
                        ->nullable()
                        ->constrained()
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
            $table->foreignIdFor(Department::class)
                        ->nullable()
                        ->constrained()
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
            $table->decimal('subtotal')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('total_gst')->default(0);
            $table->decimal('net_total')->default(0);
            $table->string('status')->default(PurchaseOrderStatus::Approved);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
