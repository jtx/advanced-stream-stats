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
        Schema::create('braintree_cards_on_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('braintree_customer_id')->index();
            $table->string('token');
            $table->string('masked_number');
            $table->date('expiration_date');
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
        Schema::dropIfExists('braintree_cards_on_file');
    }
};
