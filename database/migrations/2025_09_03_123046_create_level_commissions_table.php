<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('level_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique();
            $table->string('level_name');
            $table->decimal('min_invest', 15, 2);
            $table->integer('direct_referral')->default(0);
            $table->decimal('commission', 5, 2)->default(0);
            $table->timestamps();
        });
                $defaultLevels = [];
                for ($i = 1; $i <= 10; $i++) {
                    $defaultLevels[] = [
                        'level' => $i,
                        'level_name' => "Level $i",
                        'min_invest' => 0,
                        'direct_referral' => 0,
                        'commission' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('level_commissions')->insert($defaultLevels);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_commissions');
    }
};
