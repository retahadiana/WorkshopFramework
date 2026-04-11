<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'foto_blob')) {
                    $table->binary('foto_blob')->nullable()->after('foto_path');
                }
                if (!Schema::hasColumn('customers', 'storage_method')) {
                    $table->string('storage_method')->nullable()->after('foto_blob');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'storage_method')) {
                    $table->dropColumn('storage_method');
                }
                if (Schema::hasColumn('customers', 'foto_blob')) {
                    $table->dropColumn('foto_blob');
                }
            });
        }
    }
};
