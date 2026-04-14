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
        Schema::table('guardians', function (Blueprint $table) {
            $table->string('invitation_token')->nullable()->after('occupation');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
            $table->index('invitation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropIndex(['invitation_token']);
            $table->dropColumn(['invitation_token', 'invitation_expires_at']);
        });
    }
};
