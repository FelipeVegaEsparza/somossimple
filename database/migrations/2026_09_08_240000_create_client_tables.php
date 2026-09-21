<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('phone_normalized')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'phone_normalized']);
        });

        Schema::create('client_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['business_id', 'name']);
        });

        Schema::create('client_tag', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('client_tags')->cascadeOnDelete();
            $table->primary(['client_id', 'tag_id']);
        });

        Schema::create('client_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('channel'); // email
            $table->boolean('granted')->default(false);
            $table->string('source')->nullable(); // reservation | voluntary | manual
            $table->timestamps();

            $table->unique(['client_id', 'channel']);
        });

        Schema::create('client_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_notes');
        Schema::dropIfExists('client_consents');
        Schema::dropIfExists('client_tag');
        Schema::dropIfExists('client_tags');
        Schema::dropIfExists('clients');
    }
};
