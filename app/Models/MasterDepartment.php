<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'master_departments';

    protected $fillable = ['division_id', 'name'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(MasterDivision::class, 'division_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}
