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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 15, 2);
            $table->enum('remark',['deposit','withdrawal','transfer','referral_commission','interest','package_purchased','convert','activation','generation_income','code_purchased']);
            $table->enum('type',['-','+']);
            $table->enum('status',['Pending','Paid','Completed','Rejected']);
            $table->string('details')->nullable();
            $table->string('currency')->default('USDT');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
