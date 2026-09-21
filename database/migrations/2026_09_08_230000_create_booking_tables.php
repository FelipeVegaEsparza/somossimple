<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedBigInteger('price')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('booking_week_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = lunes ... 6 = domingo
            $table->time('open_time');
            $table->time('close_time');
            $table->timestamps();

            $table->unique(['business_id', 'day_of_week', 'open_time', 'close_time'], 'booking_hours_unique');
        });

        Schema::create('booking_day_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['business_id', 'date']);
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('booking_services')->nullOnDelete();
            $table->string('service_name');
            $table->unsignedSmallInteger('duration_minutes');
            $table->dateTime('starts_at');
            $table->string('client_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['business_id', 'starts_at']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('booking_day_offs');
        Schema::dropIfExists('booking_week_hours');
        Schema::dropIfExists('booking_services');
    }
};
