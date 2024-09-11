<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';

    protected $fillable = [
        'service_id',
        'date',
        'time',
        'totalAmount',
        'user_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'time' => 'string'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->embedsMany(Service::class, 'service_id');
    }
}