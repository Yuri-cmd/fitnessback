<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weight_logs', function (Blueprint $table) {
            $table->timestamp('logged_at')->useCurrent()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('weight_logs', function (Blueprint $table) {
            $table->dropColumn('logged_at');
        });
    }
};
