<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // El dominio maneja su propia fecha exacta
    
    protected $fillable = [
        'id',
        'product_id',
        'quantity',
        'total_price',
        'status',
        'created_at'
    ];
}