<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticketera_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('category')->nullable();
            $table->string('organizer')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('venue_city')->nullable();
            $table->text('venue_info')->nullable();
            $table->string('status')->default('draft'); // draft | published | paused | finished | cancelled
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });

        Schema::create('ticketera_ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('ticketera_events')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('sold')->default(0);
            $table->dateTime('sales_start_at')->nullable();
            $table->dateTime('sales_end_at')->nullable();
            $table->unsignedInteger('purchase_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('ticketera_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('ticketera_events')->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('buyer_name');
            $table->string('buyer_lastname')->nullable();
            $table->string('buyer_email');
            $table->string('buyer_phone')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('commission')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('status')->default('pending'); // pending | paid | cancelled | refunded | failed
            $table->string('payment_provider')->nullable();
            $table->string('payment_reference')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
            $table->index(['buyer_email', 'number']);
        });

        Schema::create('ticketera_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('ticketera_events')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('ticketera_orders')->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->constrained('ticketera_ticket_types')->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('token')->unique();
            $table->string('holder_name');
            $table->unsignedBigInteger('price')->default(0);
            $table->string('status')->default('issued'); // issued | used | cancelled | refunded
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
        });

        Schema::create('ticketera_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('token')->unique();
            $table->string('role')->default('access'); // access
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ticketera_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('ticketera_events')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('ticketera_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('ticketera_staff')->nullOnDelete();
            $table->string('device')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'created_at']);
        });

        Schema::create('ticketera_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('ticketera_orders')->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('status')->default('pending'); // pending | done | rejected
            $table->dateTime('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticketera_refunds');
        Schema::dropIfExists('ticketera_accesses');
        Schema::dropIfExists('ticketera_staff');
        Schema::dropIfExists('ticketera_tickets');
        Schema::dropIfExists('ticketera_orders');
        Schema::dropIfExists('ticketera_ticket_types');
        Schema::dropIfExists('ticketera_events');
    }
};
