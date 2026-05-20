<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar FK nullable para poder poblarla antes de hacerla NOT NULL
        Schema::table('notification_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('notification_type_id')->nullable()->after('id');
        });

        // 2. Poblar FK buscando el tipo por type + context
        DB::statement('
            UPDATE notification_messages nm
            JOIN notification_types nt
                ON nt.type = nm.type
                AND (nt.context <=> nm.context)
            SET nm.notification_type_id = nt.id
        ');

        // 3. Hacer la columna NOT NULL y agregar FK constraint
        DB::statement('ALTER TABLE notification_messages MODIFY notification_type_id BIGINT UNSIGNED NOT NULL');

        Schema::table('notification_messages', function (Blueprint $table) {
            $table->foreign('notification_type_id')
                  ->references('id')
                  ->on('notification_types')
                  ->onDelete('cascade');
        });

        // 4. Quitar las columnas type y context (ya innecesarias)
        Schema::table('notification_messages', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['context']);
            $table->dropColumn(['type', 'context']);
        });
    }

    public function down(): void
    {
        Schema::table('notification_messages', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->after('id');
            $table->string('context', 30)->nullable()->after('type');
        });

        DB::statement('
            UPDATE notification_messages nm
            JOIN notification_types nt ON nt.id = nm.notification_type_id
            SET nm.type = nt.type, nm.context = nt.context
        ');

        Schema::table('notification_messages', function (Blueprint $table) {
            $table->dropForeign(['notification_type_id']);
            $table->dropColumn('notification_type_id');
            $table->index('type');
            $table->index('context');
        });
    }
};
