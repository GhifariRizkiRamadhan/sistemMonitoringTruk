<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalExpenses extends Model
{
    use HasFactory;

    protected $table = 'operational_expenses';

    protected $fillable = [
        'truck_id', 'operational_type_id', 'description', 'date', 
        'quantity', 'price', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
