<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MageCustomer extends Model
{
    use HasFactory;

    protected $casts = [
        'json' => 'array'
    ];

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }
}
