<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'id','name', 'created_at'
    ];

    public $timestamps = false; // Disable timestamps if 'created_at' is managed manually

    public function trucks()
    {
        return $this->belongsToMany(Trucks::class, 'truck_drivers', 'driver_id', 'truck_id');
    }
}
