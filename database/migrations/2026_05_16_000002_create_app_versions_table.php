<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['ios', 'android']);
            $table->string('latest_version', 20);   // última versión disponible
            $table->string('minimum_version', 20);  // mínima soportada (force update)
            $table->string('store_url');
            $table->text('release_notes')->nullable();
            $table->timestamps();

            $table->unique('platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
