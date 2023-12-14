<?php

use App\Filament\Enum\PurchaseOrderStatus;
use App\Filament\Enum\PurchaseRequestStatus;
use App\Models\Department;
use App\Models\ExpenseAccount;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable();
            $table->foreignIdFor(Department::class)->nullable();
            $table->foreignIdFor(ExpenseAccount::class)->nullable();
            $table->foreignIdFor(Store::class)
                        ->nullable()
                        ->constrained()
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
            $table->decimal('subtotal')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('total_gst')->default(0);
            $table->decimal('net_total')->default(0);
            $table->foreignIdFor(User::class,'requested_by_id')
                        ->nullable()
                        ->constrained("users")
                        ->restrictOnUpdate()
                        ->restrictOnDelete();
            $table->string('budget_reference_no')->nullable();
            $table->mediumText('purpose')->nullable();
            $table->string('status')->default(PurchaseRequestStatus::Pending);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
