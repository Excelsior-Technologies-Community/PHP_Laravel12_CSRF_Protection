<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'ip_address',
        'submission_type',
    ];
}