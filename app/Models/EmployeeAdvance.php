<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAdvance extends Model
{
    protected $fillable = ['employee_id', 'amount', 'advance_date','created_by'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
