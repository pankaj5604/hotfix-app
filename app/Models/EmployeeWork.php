<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWork extends Model
{

     use HasFactory;

     protected $table = 'employee_work';

    protected $fillable = [
        'created_by',
        'employee_id',
        'product_id',
        'employee_rate',
        'product_rate',
        'total_sadi',
        'employee_total',
        'product_total',
        'work_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
