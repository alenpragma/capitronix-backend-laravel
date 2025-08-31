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
        Schema::create('daily_cron_time', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('next_cron_time');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('daily_cron_time')->insert([
            'name' => 'daily',
            'next_cron_time' => now(),
            'created_at' => \Illuminate\Support\Carbon::now(),
        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cron_time');
    }
};
