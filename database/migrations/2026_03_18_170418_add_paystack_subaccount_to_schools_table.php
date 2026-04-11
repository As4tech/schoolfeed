<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('schools', 'paystack_subaccount_code')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('paystack_subaccount_code')->nullable()->after('phone');
            });
        }
        
        if (!Schema::hasColumn('schools', 'paystack_bank_code')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('paystack_bank_code')->nullable()->after('paystack_subaccount_code');
            });
        }
        
        if (!Schema::hasColumn('schools', 'paystack_account_number')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('paystack_account_number')->nullable()->after('paystack_bank_code');
            });
        }
        
        if (!Schema::hasColumn('schools', 'paystack_account_name')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('paystack_account_name')->nullable()->after('paystack_account_number');
            });
        }
        
        if (!Schema::hasColumn('schools', 'paystack_settlement_bank')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('paystack_settlement_bank')->nullable()->after('paystack_account_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'paystack_subaccount_code',
                'paystack_bank_code',
                'paystack_account_number',
                'paystack_account_name',
                'paystack_settlement_bank',
            ]);
        });
    }
};
