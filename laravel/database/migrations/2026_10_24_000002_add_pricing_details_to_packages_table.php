<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('duration')->nullable()->after('description');
            $table->string('price_note')->nullable()->default('Mulai dari')->after('duration');
            $table->string('price_unit')->nullable()->after('price');
            $table->string('old_price')->nullable()->after('price_unit');
            $table->boolean('is_featured')->default(false)->after('features');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['description', 'duration', 'price_note', 'price_unit', 'old_price', 'is_featured']);
        });
    }
};
