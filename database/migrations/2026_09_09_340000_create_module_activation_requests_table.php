<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_activation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->string('action'); // activate | deactivate
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['business_id', 'module'], 'module_req_business_module_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_activation_requests');
    }
};
