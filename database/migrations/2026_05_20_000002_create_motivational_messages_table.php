<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivational_messages', function (Blueprint $table) {
            $table->id();
            // morning | evening_trained | evening_not_trained | birthday
            $table->string('type', 30)->index();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motivational_messages');
    }
};
