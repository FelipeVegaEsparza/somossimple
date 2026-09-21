<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('kind'); // social | button
            $table->string('network')->nullable(); // instagram, facebook, ...
            $table->string('label')->nullable(); // solo para botones personalizados
            $table->string('url');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('business_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_gallery');
        Schema::dropIfExists('profile_links');
    }
};
