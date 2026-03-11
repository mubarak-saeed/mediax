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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_invoice_id')->nullable()->unique()->after('id');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_invoice_id');
            $table->string('currency', 3)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['stripe_invoice_id']);
            $table->dropColumn(['stripe_invoice_id', 'stripe_payment_intent_id', 'currency']);
        });
    }
};
