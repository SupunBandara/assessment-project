<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoiceNo',
        'customerName',
        'date',
        'status',
        'totalAmount'
    ];


    protected $casts = [
        'date' => 'date:Y-m-d'
    ];


    public function items(){

        return $this->hasMany(Sale::class, 'invoiceId', 'id');
    }

}
