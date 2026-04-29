<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $imported_by
 * @property string $file_name
 * @property int $total_rows
 * @property int $success_rows
 * @property int $error_rows
 * @property array|null $summary
 * @property string $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Import extends Model
{
    use SoftDeletes;

    protected $fillable = ['imported_by', 'file_name', 'total_rows', 'success_rows', 'error_rows', 'summary', 'status', 'error_message'];
    protected $casts = [
        'summary' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rows()
    {
        return $this->hasMany(ImportRow::class);
    }
}
