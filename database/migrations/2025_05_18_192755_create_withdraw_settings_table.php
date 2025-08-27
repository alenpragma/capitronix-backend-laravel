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
        Schema::create('withdraw_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('min_withdraw');
            $table->integer('max_withdraw');
            $table->integer('charge');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        DB::table('withdraw_settings')->insert([
            'min_withdraw' => 10, 
            'max_withdraw' => 5000, 
            'charge'       => 5, 
            'status'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_settings');
    }
};
