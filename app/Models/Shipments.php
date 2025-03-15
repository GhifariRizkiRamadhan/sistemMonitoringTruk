<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipments extends Model
{
    use HasFactory;

    protected $table = 'shipments';

    protected $fillable = [
        'truck_id', 'cargo_type_id', 'travel_money_date', 'loading_date', 
        'unloading_date', 'travel_money', 'tonnage', 'wage_per_ton', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
