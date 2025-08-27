<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'deduct' to the enum values
        DB::statement("ALTER TABLE transactions MODIFY remark ENUM('deposit','withdrawal','transfer','referral_commission','interest','package_purchased','convert','activation','generation_income','code_purchased','deduct')");
    }

    public function down(): void
    {
        // Remove 'deduct' if rollback
        DB::statement("ALTER TABLE transactions MODIFY remark ENUM('deposit','withdrawal','transfer','referral_commission','interest','package_purchased','convert','activation','generation_income','code_purchased')");
    }
};