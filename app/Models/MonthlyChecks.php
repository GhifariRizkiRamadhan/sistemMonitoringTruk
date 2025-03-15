<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyChecks extends Model
{
    use HasFactory;

    protected $table = 'monthly_checks';

    protected $fillable = [
        'truck_id', 'check_date', 'description', 'month', 'year', 
        'tire_condition', 'current_km', 'service_km_remaining', 'brake_condition',
        'cabin_condition', 'cargo_area_condition', 'lights_condition', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
