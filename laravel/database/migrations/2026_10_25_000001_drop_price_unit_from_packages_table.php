<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Satuan harga ("/ sesi") tidak dipakai lagi: durasi sesi sudah menjelaskannya. */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('price_unit');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('price_unit')->nullable()->after('price_style');
        });
    }
};
