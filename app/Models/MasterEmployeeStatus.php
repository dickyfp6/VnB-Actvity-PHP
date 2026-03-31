<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterEmployeeStatus extends Model
{
    use SoftDeletes;

    protected $table = 'master_employee_statuses';

    protected $fillable = ['name'];
}
