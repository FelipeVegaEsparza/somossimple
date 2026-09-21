<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_codes', function (Blueprint $table) {
            $table->timestamp('activated_at')->nullable()->after('delivered_at');
        });

        // Los códigos antes "entregados" (con negocio asignado) pasan a "activados".
        DB::table('physical_codes')
            ->where('status', 'delivered')
            ->update(['status' => 'activated', 'activated_at' => DB::raw('COALESCE(delivered_at, updated_at)')]);
    }

    public function down(): void
    {
        DB::table('physical_codes')
            ->where('status', 'activated')
            ->update(['status' => 'delivered']);
        Schema::table('physical_codes', function (Blueprint $table) {
            $table->dropColumn('activated_at');
        });
    }
};
