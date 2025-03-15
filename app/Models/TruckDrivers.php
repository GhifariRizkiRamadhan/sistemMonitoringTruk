<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckDrivers extends Model
{
    use HasFactory;

    protected $table = 'truck_drivers';

    protected $fillable = [
        'truck_id', 'driver_id', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
