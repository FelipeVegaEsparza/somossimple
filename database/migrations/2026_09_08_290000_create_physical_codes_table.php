<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_codes', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // qr | nfc | qr_nfc
            $table->string('serial')->unique();
            $table->string('status')->default('available'); // available | delivered
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'business_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_codes');
    }
};
