<?php

use App\Models\PurchaseOrder;
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
            $table->foreignIdFor(PurchaseOrder::class);
            $table->string('reference_no')->nullable();
            $table->date('received_date');
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
