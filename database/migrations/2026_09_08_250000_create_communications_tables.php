<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // promo | oferta | anuncio | noticia | cambio_horario | general
            $table->boolean('is_commercial')->default(true);
            $table->string('subject');
            $table->text('body');
            $table->string('audience'); // all | tag
            $table->string('tag_name')->nullable();
            $table->string('status')->default('draft'); // draft | sent
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('status')->default('sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_sends');
        Schema::dropIfExists('communications');
    }
};
