<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'mobile',
        'gender',
        'profile_pic',
        'status',
    ];

    public function works()
    {
        return $this->hasMany(EmployeeWork::class, 'employee_id');
    }
}