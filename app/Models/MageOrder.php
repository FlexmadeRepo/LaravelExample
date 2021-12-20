<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MageOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        "customer_id",
    ];
    protected $casts = [
        'json' => 'array'
    ];

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }
}
