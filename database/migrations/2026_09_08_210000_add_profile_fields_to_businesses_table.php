<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('logo_path')->nullable()->after('description');
            $table->string('cover_path')->nullable()->after('logo_path');
            $table->string('phone')->nullable()->after('cover_path');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('contact_email')->nullable()->after('whatsapp');
            $table->string('website')->nullable()->after('contact_email');
            $table->string('address')->nullable()->after('website');
            $table->string('map_url')->nullable()->after('address');
            $table->text('opening_hours')->nullable()->after('map_url');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'logo_path', 'cover_path', 'phone', 'whatsapp',
                'contact_email', 'website', 'address', 'map_url', 'opening_hours',
            ]);
        });
    }
};
