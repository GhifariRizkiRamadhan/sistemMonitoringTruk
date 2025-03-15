<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalTypes extends Model
{
    use HasFactory;

    protected $table = 'operational_types';

    protected $fillable = [
        'name', 'is_custom', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
