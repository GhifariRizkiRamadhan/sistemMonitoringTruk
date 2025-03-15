<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trucks extends Model
{
    use HasFactory;

    protected $table = 'trucks';

    protected $fillable = [
        'plate_number', 'category', 'purchase_date', 'description', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually

    public function drivers()
    {
        return $this->belongsToMany(Drivers::class, 'truck_drivers', 'truck_id', 'driver_id');
    }
}
