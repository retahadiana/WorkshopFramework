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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom id_google setelah email
            if (! Schema::hasColumn('users', 'id_google')) {
                $table->string('id_google', 256)->nullable()->after('email');
            }

            // Tambah kolom otp setelah remember_token
            if (! Schema::hasColumn('users', 'otp')) {
                $table->string('otp', 6)->nullable()->after('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_google')) {
                $table->dropColumn('id_google');
            }

            if (Schema::hasColumn('users', 'otp')) {
                $table->dropColumn('otp');
            }
        });
    }
};
