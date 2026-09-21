<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_prices', function (Blueprint $table) {
            $table->id();
            $table->string('module')->unique();
            $table->unsignedBigInteger('price_monthly');
            $table->timestamps();
        });

        Schema::create('module_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->unsignedBigInteger('amount');
            $table->date('paid_on');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'module']);
        });

        Schema::table('module_access', function (Blueprint $table) {
            $table->timestamp('activated_at')->nullable()->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('module_access', function (Blueprint $table) {
            $table->dropColumn('activated_at');
        });
        Schema::dropIfExists('module_payments');
        Schema::dropIfExists('module_prices');
    }
};
