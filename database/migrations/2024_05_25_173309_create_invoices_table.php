<?php

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cost_name')->nullable();
            $table->string('cost_address')->nullable();
            $table->string('cost_number')->nullable();
            $table->string('bill_number');
            $table->string('table_number')->nullable();
            $table->dateTime('date_time');
            $table->string('waiter_code')->nullable();
            $table->string('cashier_name')->nullable();
            $table->decimal('sub_total', 8, 2);
            $table->decimal('sgst', 8, 2)->nullable();
            $table->decimal('cgst', 8, 2)->nullable();
            $table->decimal('total_discount', 8, 2)->default(0);
            $table->decimal('total_amount', 8, 2);
            // payment_mode: 1 = cash, 2 = card, 3 = upi (default = 1)
            $table->tinyInteger('payment_mode')->default(1)->comment('1=Cash, 2=Card, 3=UPI');
            $table->string('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
