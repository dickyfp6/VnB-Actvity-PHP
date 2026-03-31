<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use SoftDeletes;

    protected $fillable = ['file_path', 'total_rows', 'success_rows', 'error_rows', 'status'];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function rows()
    {
        return $this->hasMany(ImportRow::class);
    }
}
