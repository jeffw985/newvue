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
        Schema::table('irrigation', function (Blueprint $table) {
            $table->dropColumn([
                'paid',
                'paid_amount',
                'payment_type',
                'prepayment_waived',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('irrigation', function (Blueprint $table) {
            $table->boolean('paid')->default(false);
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('payment_type')->nullable();
            $table->boolean('prepayment_waived')->default(false);
        });
    }
};
