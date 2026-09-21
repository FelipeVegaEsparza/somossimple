<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->unique(['business_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_access');
    }
};
