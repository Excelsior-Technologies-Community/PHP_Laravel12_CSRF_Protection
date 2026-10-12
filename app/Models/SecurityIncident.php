<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SecurityIncident extends Model
{
    protected static function booted()
    {
        static::checkAndCreateTable();
    }

    public static function checkAndCreateTable()
    {
        try {
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
        } catch (\Throwable $e) {
            // Silence if already created or connection fails
        }
    }

    protected $fillable = [
        'incident_type',
        'ip_address',
        'target_url',
        'user_agent',
        'payload_preview',
        'status',
    ];
}
