<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LEGACY_COLORS = ['blue' => '#0022ff', 'orange' => '#ff6738', 'pink' => '#ff79b8', 'green' => '#b7e433'];

    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('price_amount')->default(0)->after('price_note');
            $table->unsignedInteger('old_price_amount')->nullable()->after('price_amount');
            $table->string('price_style', 2)->default('k')->after('old_price_amount');
            $table->unsignedSmallInteger('duration_value')->nullable()->after('description');
            $table->string('duration_unit', 5)->default('menit')->after('duration_value');
        });

        foreach (DB::table('packages')->get() as $row) {
            DB::table('packages')->where('id', $row->id)->update([
                'price_amount' => $this->toAmount($row->price),
                'old_price_amount' => $row->old_price ? $this->toAmount($row->old_price) : null,
                'price_style' => stripos((string) $row->price, 'k') !== false ? 'k' : 'rp',
                'duration_value' => preg_match('/(\d+)/', (string) $row->duration, $m) ? (int) $m[1] : null,
                'duration_unit' => preg_match('/det|sec|dtk/i', (string) $row->duration) ? 'detik' : 'menit',
                'color' => self::LEGACY_COLORS[$row->color] ?? $row->color,
            ]);
        }
        DB::table('frames')->get()->each(fn ($f) => DB::table('frames')->where('id', $f->id)->update([
            'color' => self::LEGACY_COLORS[$f->color] ?? $f->color,
        ]));

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['price', 'old_price', 'duration']);
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('price')->default('0');
            $table->string('old_price')->nullable();
            $table->string('duration')->nullable();
        });

        foreach (DB::table('packages')->get() as $row) {
            $text = fn (?int $n) => $n === null ? null : (($row->price_style === 'k' && $n % 1000 === 0) ? ($n / 1000).'K' : number_format($n, 0, ',', '.'));
            DB::table('packages')->where('id', $row->id)->update([
                'price' => $text($row->price_amount),
                'old_price' => $text($row->old_price_amount),
                'duration' => $row->duration_value ? $row->duration_value.' '.$row->duration_unit : null,
            ]);
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['price_amount', 'old_price_amount', 'price_style', 'duration_value', 'duration_unit']);
        });
    }

    /** "35K" -> 35000, "50.000" -> 50000, "Rp 1,5K" -> 1500 */
    private function toAmount(?string $text): int
    {
        $text = strtolower(trim((string) $text));
        $text = preg_replace('/^rp\.?\s*/', '', $text);

        if (str_ends_with($text, 'k')) {
            return (int) round((float) str_replace(',', '.', rtrim($text, 'k')) * 1000);
        }

        return (int) preg_replace('/\D+/', '', $text);
    }
};
