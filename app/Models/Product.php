<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     use HasFactory;

    protected $fillable = [
        'type',
        'product_rate',
        'employee_rate'
    ];

    public function works()
    {
        return $this->hasMany(EmployeeWork::class);
    }
}
