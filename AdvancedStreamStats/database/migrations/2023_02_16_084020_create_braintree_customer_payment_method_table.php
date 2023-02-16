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
        Schema::create('braintree_customer_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('braintree_customer_id')->index();
            $table->string('braintree_payment_method_token');
            $table->string('payment_method_meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('braintree_customer_id')->references('id')->on('braintree_customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('braintree_customer_payment_methods');
    }
};
