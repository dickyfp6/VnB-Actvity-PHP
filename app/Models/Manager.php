<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $employee_number
 * @property string|null $company
 * @property string|null $division
 * @property int|null $division_id
 * @property int|null $department_id
 * @property string $status
 * @property int|null $user_id
 */
class Manager extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'employee_number', 'company', 'division', 'division_id', 'department_id', 'status', 'user_id'];

    // Relationships
    public function division(): BelongsTo
    {
        return $this->belongsTo(MasterDivision::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function functionalEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_functional_id');
    }

    public function operationalEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_operational_id');
    }
}
