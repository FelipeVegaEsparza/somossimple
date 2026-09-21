<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('nueva');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_requests');
    }
};
