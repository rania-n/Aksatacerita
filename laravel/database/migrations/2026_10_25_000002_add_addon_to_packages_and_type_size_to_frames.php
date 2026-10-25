<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('addon_price_amount')->nullable()->after('old_price_amount');
            $table->string('addon_text')->nullable()->after('addon_price_amount');
        });

        Schema::table('frames', function (Blueprint $table) {
            $table->string('type')->nullable()->after('label');
            $table->string('size', 12)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('packages', fn (Blueprint $table) => $table->dropColumn(['addon_price_amount', 'addon_text']));
        Schema::table('frames', fn (Blueprint $table) => $table->dropColumn(['type', 'size']));
    }
};
