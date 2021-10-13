<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'addressLine1',
        'addressLine2',
        'telNo',
        'email',
        'city',
        'district',
        'status'
    ];

}
