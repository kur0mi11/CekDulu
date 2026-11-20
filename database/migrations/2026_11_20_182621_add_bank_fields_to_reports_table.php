<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('bank_name', 50)->nullable()->after('reported_type');
            $table->string('account_holder', 100)->nullable()->after('reported_value');
            $table->index(['reported_type', 'bank_name', 'reported_value']);
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['reported_type', 'bank_name', 'reported_value']);
            $table->dropColumn(['bank_name', 'account_holder']);
        });
    }
};
