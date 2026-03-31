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
 * @property string $status
 * @property int|null $user_id
 */
class Manager extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'employee_number', 'company', 'division', 'status', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function functionalNewHires(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_functional_id');
    }

    public function operationalNewHires(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_operational_id');
    }
}
