<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoTypes extends Model
{
    use HasFactory;

    protected $table = 'cargo_types';

    protected $fillable = [
       'id','name', 'is_custom', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually
}
