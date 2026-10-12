<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('security_incidents')) {
            Schema::create('security_incidents', function (Blueprint $table) {
                $table->id();
                $table->string('incident_type')->default('CSRF_MISMATCH');
                $table->string('ip_address')->nullable();
                $table->string('target_url')->nullable();
                $table->text('user_agent')->nullable();
                $table->text('payload_preview')->nullable();
                $table->string('status')->default('BLOCKED');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_incidents');
    }
};
