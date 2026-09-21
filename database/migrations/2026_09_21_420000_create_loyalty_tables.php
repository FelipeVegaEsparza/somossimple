<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('points'); // points | visits | stamps | rewards
            $table->string('unit_name')->default('puntos');
            $table->unsignedBigInteger('earn_amount')->nullable(); // monto que otorga unidades (solo puntos)
            $table->unsignedInteger('earn_units')->default(1);
            $table->boolean('is_active')->default(true);
            $table->string('card_primary_color')->default('#437eff');
            $table->string('card_secondary_color')->default('#1a2233');
            $table->string('card_text_primary')->nullable();
            $table->string('card_text_secondary')->nullable();
            $table->timestamps();
        });

        Schema::create('loyalty_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('token')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('points')->default(0);
            $table->unsignedInteger('visits')->default(0);
            $table->unsignedInteger('stamps')->default(0);
            $table->string('status')->default('active'); // active | inactive
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'code']);
        });

        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('requirement_type')->default('points'); // points | visits | stamps
            $table->unsignedInteger('requirement_units');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_member_id')->constrained('loyalty_members')->cascadeOnDelete();
            $table->foreignId('reward_id')->nullable()->constrained('loyalty_rewards')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->integer('units')->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
        });

        Schema::create('loyalty_wallet_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_member_id')->constrained('loyalty_members')->cascadeOnDelete();
            $table->string('platform'); // apple | google
            $table->string('card_identifier')->nullable();
            $table->string('status')->default('not_added'); // not_added | generated | active | update_pending | error
            $table->timestamp('last_synced_at')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamps();

            $table->unique(['loyalty_member_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_wallet_cards');
        Schema::dropIfExists('loyalty_activities');
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('loyalty_members');
        Schema::dropIfExists('loyalty_programs');
    }
};
